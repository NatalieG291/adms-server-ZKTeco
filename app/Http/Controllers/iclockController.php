<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use dateTime;
use Iluminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PDO;

class iclockController extends Controller
{
    public function __invoke(Request $request)
    {
    }

    // ============================
    //  HANDSHAKE (envío de opciones)
    // ============================
    public function handshake(Request $request)
    {
        // Log de request
        $data = [
            'url' => json_encode($request->all()),
            'data' => $request->getContent(),
            'sn'   => $request->input('SN'),
            'option' => $request->input('option'),
        ];
        DB::table('device_log')->insert($data);

        $sn = $request->input('SN');

        // ============================
        // 1. Guardar TimeZone enviado por el lector (si existe)
        // ============================
        if ($request->has('TimeZone')) {
            DB::table('devices')
                ->where('no_sn', $sn)
                ->update(['timezone' => $request->input('TimeZone')]);
        }

        // ============================
        // 2. Actualizar estado online
        // ============================
        DB::table('devices')->updateOrInsert(
            ['no_sn' => $sn],
            ['online' => now()]
        );

        // ============================
        // 3. Obtener zona horaria para enviar al lector
        // ============================
        $device = DB::table('devices')->where('no_sn', $sn)->first();
        $timezone = $device && $device->timezone !== null ? $device->timezone : -6;

        // ============================
        // 4. Respuesta ADMS
        // ============================
        $r =
            "GET OPTION FROM: {$sn}\r\n" .
            "Stamp=9999\r\n" .
            "OpStamp=" . time() . "\r\n" .
            "ErrorDelay=60\r\n" .
            "Delay=30\r\n" .
            "ResLogDay=18250\r\n" .
            "ResLogDelCount=10000\r\n" .
            "ResLogCount=50000\r\n" .
            "TransTimes=00:00;14:05\r\n" .
            "TransInterval=1\r\n" .
            "TransFlag=1111000000\r\n" .
            "TimeZone={$timezone}\r\n" .   // ? ENVÍO DE ZONA HORARIA
            "Realtime=1\r\n" .
            "Encrypt=0";

        return $r;
    }

    // ============================
    //  RECEPCIÓN DE REGISTROS
    // ============================
public function receiveRecords(Request $request)
{
    $a = $request->all();
    if(str_contains($a['table'], 'ATTPHOTO')) {
        return $this->fdata($request);
    }
    
    DB::table('finger_log')->insert([
        'url'  => json_encode($request->all()),
        'data' => $request->getContent(),
    ]);

    try {
        $raw = trim($request->getContent());

        
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $clean = [];
        $buffer = '';

        foreach ($lines as $line) {
            $trim = trim($line);

            
            if (str_starts_with($trim, 'FP')) {
                if ($buffer !== '') {
                    $clean[] = $buffer;
                }
                $buffer = $trim;
            } else {
                
                if ($buffer !== '') {
                    $buffer .= $trim;
                }
            }
        }

        if ($buffer !== '') {
            $clean[] = $buffer;
        }

        $tot = 0;

        
        foreach ($clean as $fpLine) {
            if (!str_starts_with(trim($fpLine), 'FP')) {
                continue;
            }

            preg_match('/PIN=(\d+)/', $fpLine, $pin);
            preg_match('/FID=(\d+)/', $fpLine, $fid);
            preg_match('/Size=(\d+)/', $fpLine, $size);
            preg_match('/Valid=(\d+)/', $fpLine, $valid);
            preg_match('/TMP=([\s\S]+)/', $fpLine, $tmp);

            $template = trim($tmp[1] ?? '');

            DB::table('fingerprints')->updateOrInsert(
                [
                    'pin' => $pin[1] ?? null,
                    'fid' => $fid[1] ?? null,
                ],
                [
                    'size'       => $size[1] ?? 0,
                    'valid'      => $valid[1] ?? 0,
                    'template'   => $template,
                    'updated_at' => now(),
                ]
            );

            $tot++;
        }

        
        if (!str_contains($raw, "\t") && !str_contains($raw, "\n")) {

            $tokens = preg_split('/\s+/', $raw);
            $records = array_chunk($tokens, 10);

            foreach ($records as $data) {
                if (count($data) < 3) continue;

                if(!str_contains($data[0], 'OPLOG') && !str_contains($data[0], '~DeviceName')) {

                    $lector = DB::connection('giro')
                        ->table('Supervisor_giro.Lectores_adms')
                        ->where('NUMERO_SERIE', '=', $request->input('SN'))
                        ->get();

                    if ($lector->isEmpty()) {
                        $g = DB::connection('giro')
                            ->table('Supervisor_giro.Lectores_adms')
                            ->insert([
                                'NUMERO_SERIE' => $request->input('SN'),
                                'DESCRIPCION'  => 'Lector desde iclockController',
                            ]);
                    }

                    if(DB::connection('giro')
                        ->table('Supervisor_giro.BitacoraRegistros')
                        ->where('CLAVE', $data[0])
                        ->where('FECHA', $data[1])
                        ->exists()) {
                            continue;
                    }
                    
                    DB::connection('giro')
                        ->table('Supervisor_giro.BitacoraRegistros')
                        ->insert([
                            'CLAVE' => $data[0],
                            'FECHA' => $data[1] . ' ' . $data[2],
                            'FECHA_LECTURA' => now(),
                            'LECTOR' => $request->input('SN'),
                            'REGISTRADO' => null,
                        ]);

                    $q['sn']         = $request->input('SN');
                    $q['table']      = $request->input('table');
                    $q['stamp']      = $request->input('Stamp');
                    $q['employee_id'] = $data[0];
                    $q['timestamp']   = $data[1] . ' ' . $data[2];

                    $q['status1'] = $this->validateAndFormatInteger($data[3] ?? null);
                    $q['status2'] = $this->validateAndFormatInteger($data[4] ?? null);
                    $q['status3'] = $this->validateAndFormatInteger($data[5] ?? null);
                    $q['status4'] = $this->validateAndFormatInteger($data[6] ?? null);
                    $q['status5'] = $this->validateAndFormatInteger($data[7] ?? null);

                    $q['created_at'] = now();
                    $q['updated_at'] = now();

                    DB::table('attendances')->insert($q);
                }
                $tot++;
            }

            return "OK: " . $tot;
        }

        
        $rows = preg_split('/\r\n|\r|\n/', $raw);

        foreach ($rows as $row) {
            if (empty(trim($row))) continue;
            if (str_starts_with(trim($row), 'FP')) continue;

            $data = explode("\t", $row);
            if (count($data) < 2) continue;

                if(!str_contains($data[0], 'OPLOG') && !str_contains($data[0], '~DeviceName')) {
                    $lector = DB::connection('giro')
                        ->table('Supervisor_giro.Lectores_adms')
                        ->where('NUMERO_SERIE', '=', $request->input('SN'))
                        ->get();

                    if ($lector->isEmpty()) {
                        $g = DB::connection('giro')
                            ->table('Supervisor_giro.Lectores_adms')
                            ->insert([
                                'NUMERO_SERIE' => $request->input('SN'),
                                'DESCRIPCION'  => 'Lector desde iclockController',
                            ]);
                    }
                    
                    if(DB::connection('giro')
                        ->table('Supervisor_giro.BitacoraRegistros')
                        ->where('CLAVE', $data[0])
                        ->where('FECHA', $data[1])
                        ->exists()) {
                            continue;
                    }
                    DB::connection('giro')
                        ->table('Supervisor_giro.BitacoraRegistros')
                        ->insert([
                            'CLAVE' => $data[0],
                            'FECHA' => $data[1],
                            'FECHA_LECTURA' => now(),
                            'LECTOR' => $lector[0]->CLAVE,
                            'REGISTRADO' => null,
                        ]);
                    $q['sn']         = $request->input('SN');
                    $q['table']      = $request->input('table');
                    $q['stamp']      = $request->input('Stamp') ?? 99;
                    $q['employee_id'] = $data[0];
                    $q['timestamp']   = $data[1];

                    if (count($data) > 7) {
                        $q['status1'] = $this->validateAndFormatInteger($data[3] ?? null);
                        $q['status2'] = $this->validateAndFormatInteger($data[4] ?? null);
                        $q['status3'] = $this->validateAndFormatInteger($data[5] ?? null);
                        $q['status4'] = $this->validateAndFormatInteger($data[6] ?? null);
                        $q['status5'] = $this->validateAndFormatInteger($data[7] ?? null);
                    }

                    $q['created_at'] = now();
                    $q['updated_at'] = now();

                    DB::table('attendances')->insert($q);
              }


            $tot++;
        }

        return "OK: " . $tot;

    } catch (\Throwable $e) {
        DB::table('error_log')->insert(['data' => $e->getMessage(), 'created_at' => now(), 'updated_at' => now()]);
        report($e);
        return "ERROR";
    }
}

/////////////////////////////////
// RECEPCIÓN DE FOTOS
/////////////////////////////////

public function fdata(Request $request)
{
    $raw = file_get_contents("php://input");

    // Detectar si es una foto
    if (strpos($raw, 'CMD=uploadphoto') !== false) {

        preg_match('/PIN=(.+\.jpg)/', $raw, $m1);
        preg_match('/size=(\d+)/', $raw, $m2);

        $employee_id = substr($m1[1] ?? '', strpos($m1[1],'-') + 1, 10); 
        $employee_id = substr($employee_id, 0, strpos($employee_id, '.'));

        $filename = $m1[1] ?? ('foto_' . time() . '.jpg');
        $size = intval($m2[1] ?? 0);

        $jpegStart = strpos($raw, "\xFF\xD8");
        $jpeg = $jpeg = substr($raw, $jpegStart);

        Storage::disk('public')->put("attphoto/$filename", $jpeg);
    
        // try {
        //     $pdo = DB::connection()->getPdo();

        //     $sql = "
        //         INSERT INTO attphoto (employee_id, timestamp, filename, size, photo, sn, created_at)
        //         VALUES (?, ?, ?, ?, ?, ?, GETDATE())
        //     ";

        //     $stmt = $pdo->prepare($sql);

        //     // Convertir JPEG a stream binario
        //     $stream = fopen('php://memory', 'r+');
        //     fwrite($stream, $jpeg);
        //     rewind($stream);

        //     // Bind de parámetros
        //     $stmt->bindValue(1, $employee_id);
        //     $stmt->bindValue(2, now());
        //     $stmt->bindValue(3, $filename);
        //     $stmt->bindValue(4, strlen($jpeg));
        //     $stmt->bindValue(5, $stream, PDO::PARAM_LOB);   // ⭐ CLAVE: binario real
        //     $stmt->bindValue(6, $request->SN);

        //     $stmt->execute();
        // } catch (\Throwable $e) {
        //     DB::table('error_log')->insert(['data' => $e->getMessage()]);
        //     report($e);
        //     return "ERROR";
        // }

        return "OK";
    }

    return "OK";
}

    public function test(Request $request)
    {
        $log['data'] = $request->getContent();
        DB::table('finger_log')->insert($log);
    }

    //public function getrequest(Request $request)
    //{
    //    return "OK";
    //}
    ////////////////////////////////
    // COMMANDS 		  //
    ////////////////////////////////
    public function getrequest(Request $request)
    {
        $sn = $request->input('SN');

        // Buscar el dispositivo
        $device = DB::table('devices')->where('no_sn', $sn)->first();
        if (!$device) {
            return "OK";
        }

        // Buscar comando pendiente
        $cmd = DB::table('device_commands')
            ->where('device_id', $device->id)
            ->whereNull('executed_at')
            ->whereNull('failed_at')
            ->first();

        if (!$cmd) {
            return "OK";
        }

        // Marcar como enviado
        DB::table('device_commands')
            ->where('id', $cmd->id)
            ->update([
                'executed_at' => now(),
                'updated_at' => now(),
            ]);

        // C:{ID}:{COMANDO}
        return "C:{$cmd->id}:{$cmd->command}";
    }
    public function deviceCmdResponse(Request $request)
    {
        $sn = $request->input('SN');
        $id = $request->input('ID');
        $return = $request->input('Return');
        $cmd = $request->input('CMD');

        if (!$id) {
            return "OK";
        }

        $device = DB::table('devices')->where('no_sn', $sn)->first();
        if (!$device) {
            return "OK";
        }

        // Buscar comando
        $command = DB::table('device_commands')
            ->where('id', $id)
            ->where('device_id', $device->id)
            ->first();

        if (!$command) {
            return "OK";
        }

        // Return = 0 ? ejecutado correctamente
        if ($return == "0") {
            DB::table('device_commands')
                ->where('id', $id)
                ->update([
                    'completed_at' => now(),
                    'response' => $cmd,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('device_commands')
                ->where('id', $id)
                ->update([
                    'failed_at' => now(),
                    'response' => $cmd,
                    'updated_at' => now(),
                ]);
        }

        // Respuesta estándar
        return "OK";
    }

    private function validateAndFormatInteger($value)
    {
        return isset($value) && $value !== '' ? (int)$value : null;
    }
}