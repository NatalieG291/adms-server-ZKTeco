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
    
    // Convert to UTF-8 to avoid UCS-2 encoding errors
    $url_utf8 = mb_convert_encoding(json_encode($request->all()), 'UTF-8', 'auto');
    $data_utf8 = mb_convert_encoding($request->getContent(), 'UTF-8', 'auto');
    DB::table('finger_log')->insert([
        'url'  => $url_utf8,
        'data' => $data_utf8,
    ]);

    if(str_contains($a['table'], 'ATTPHOTO') || str_contains($request->getContent(), 'USERPIC')) {
        if(str_contains($request->getContent(), 'USERPIC')){
            return $this->userpic($request);
        }
        else {
            return $this->fdata($request);
        }
    }

    if(str_contains($request->getContent(), 'USER PIN=')) {
        return $this->receiveUser($request);
    }

    try {
        $raw = trim($request->getContent());

        
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $clean = [];
        $buffer = '';

        foreach ($lines as $line) {
            $trim = trim($line);

            
            if ((str_starts_with($trim, 'FP') || str_starts_with($trim, 'FACE'))) {
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
            if (!str_starts_with(trim($fpLine), 'FP') && !str_starts_with(trim($fpLine), 'FACE')) {
                continue;
            }

            preg_match('/PIN=(\d+)/', $fpLine, $pin);
            preg_match('/FID=(\d+)/', $fpLine, $fid);
            if(preg_match('/Size=(\d+)/', $fpLine, $size)) {
                preg_match('/Size=(\d+)/', $fpLine, $size);
            }
            else {
                preg_match('/SIZE=(\d+)/', $fpLine, $size);
            }
            preg_match('/Valid=(\d+)/', $fpLine, $valid);
            preg_match('/TMP=([\s\S]+)/', $fpLine, $tmp);

            $template = trim($tmp[1] ?? '');

            if(str_starts_with(trim($fpLine), 'FP')) {
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
            } else if(str_starts_with(trim($fpLine), 'FACE')) {
                DB::table('faces')->updateOrInsert(
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
            }

            $tot++;
        }

        
        if (!str_contains($raw, "\t") && !str_contains($raw, "\n")) {

            $tokens = preg_split('/\s+/', $raw);
            $records = array_chunk($tokens, 10);

            foreach ($records as $data) {
                if (count($data) < 3) continue;

                if(!str_contains($data[0], 'OPLOG') && !str_contains($data[0], '~DeviceName') && !str_contains($data[0], 'USER') && !str_contains($data[0], 'FP') && !str_contains($data[0], 'FACE')) {

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
            if (str_starts_with(trim($row), 'FACE')) continue;

            $data = explode("\t", $row);
            if (count($data) < 2) continue;

                if(!str_contains($data[0], 'OPLOG') && !str_contains($data[0], '~DeviceName') && !str_contains($data[0], 'USER')) {
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

public function receiveUser(Request $request)
{
    $raw = trim($request->getContent());
    $lines = preg_split('/\r\n|\r|\n/', $raw);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), 'USER')) {
            preg_match('/PIN=(\d+)/', $line, $matches);
            preg_match('/Name=([^\s]+)\s+([^\s]+)/', $line, $nameMatches);
            preg_match('/Pri=(\d+)/', $line, $priMatches);
            preg_match('/Passwd=(\d+)/', $line, $passwdMatches);
            preg_match('/Card=(\d+)/', $line, $cardMatches);
            preg_match('/Grp=(\d+)/', $line, $grpMatches);
            preg_match('/TZ=(\d+)/', $line, $tzMatches);
            preg_match('/Verify=(\d+)/', $line, $verifyMatches);
            preg_match('/ViceCard=(\d+)/', $line, $viceCardMatches);
            preg_match('/StartDatetime=(\d+)/', $line, $startDatetimeMatches);
            preg_match('/EndDatetime=(\d+)/', $line, $endDatetimeMatches);
            if (isset($matches[1])) {
                $employee_id = $matches[1];
                $name = (isset($nameMatches[1]) ? $nameMatches[1] : '') . ' ' . (isset($nameMatches[2]) ? $nameMatches[2] : '');
                $pri = isset($priMatches[1]) ? $priMatches[1] : null;
                $passwd = isset($passwdMatches[1]) ? $passwdMatches[1] : null;
                $card = isset($cardMatches[1]) ? $cardMatches[1] : null;
                $grp = isset($grpMatches[1]) ? $grpMatches[1] : null;
                $tz = isset($tzMatches[1]) ? $tzMatches[1] : null;
                $verify = isset($verifyMatches[1]) ? $verifyMatches[1] : null;
                $vice_card = isset($viceCardMatches[1]) ? $viceCardMatches[1] : null;
                $start_datetime = isset($startDatetimeMatches[1]) ? $startDatetimeMatches[1] : null;
                $end_datetime = isset($endDatetimeMatches[1]) ? $endDatetimeMatches[1] : null;
                DB::table('employees')->updateOrInsert(
                    ['employee_id' => $employee_id],
                    ['name' => $name, 
                    'pri' => $pri, 
                    'passwd' => $passwd,
                    'card' => $card,
                    'group' => $grp,
                    'tz' => $tz,
                    'verify' => $verify,
                    'vice_card' => $vice_card,
                    'start_datetime' => $start_datetime,
                    'end_datetime' => $end_datetime,
                    'updated_at' => now()]
                );
            }
        }
    }

    return "OK";
}

/////////////////////////////////
// RECEPCIÓN DE FOTOS
/////////////////////////////////

public function userpic(Request $request)
{
    $raw = file_get_contents("php://input");

    // Detectar si es una foto
    if (strpos($raw, 'Content=') !== false) {

        preg_match('/FileName=(.+\.jpg)/', $raw, $m1);
        preg_match('/Size=(\d+)/', $raw, $m2);

        $employee_id = substr($m1[1] ?? '', strpos($m1[1],'-') + 1, 10); 
        $employee_id = substr($employee_id, 0, strpos($employee_id, '.'));

        $filename = $m1[1] ?? ($employee_id . '.jpg');
        $size = intval($m2[1] ?? 0);

        preg_match('/Content=([A-Za-z0-9+\/=]+)/', $raw, $m3);
        $base64 = $m3[1] ?? '';
        $jpeg = base64_decode($base64);

        Storage::disk('public')->put("userpic/$filename", $jpeg);
        DB::table('emp_photos')->updateOrInsert(
            ['employee_id' => $employee_id],
            ['photo' => $filename, 'size' => $size, 'updated_at' => now()]
        );

        return "OK";
    }

    return "OK";
}

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
        DB::table('attphoto')->insert([
            'employee_id' => $employee_id,
            'timestamp' => now(),
            'filename' => $filename,
            'size' => $size,
            'sn' => $request->input('SN'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
        $info = $request->input('INFO'); // Ejemplo: Ver 8.0.4.7-20250212,4,6,10,192.168.100.15,10,7,12,1,111,1,1,50
        $infoParts = explode(',', $info);
        $fw = isset($infoParts[0]) ? $infoParts[0] : null;
        $usr_count = isset($infoParts[1]) ? $infoParts[1] : null;
        $fp_count = isset($infoParts[2]) ? $infoParts[2] : null;
        $log_count = isset($infoParts[3]) ? $infoParts[3] : null;
        $ip_address = isset($infoParts[4]) ? $infoParts[4] : null;
        $photo_count = isset($infoParts[12]) ? $infoParts[12] : null;

        $device = DB::table('devices')->where('no_sn', $sn)->first();
        if (!$device) {
            return "OK";
        }

        if($info !== null) {
            DB::table('devices')
                ->where('id', $device->id)
                ->update([
                    'fw_version' => $fw,
                    'user_count' => $usr_count,
                    'fp_count' => $fp_count,
                    'transaction_count' => $log_count,
                    'ip_address' => $ip_address,
                    'photo_count' => $photo_count,
                    'updated_at' => now(),
                ]);
        }

        if(($log_count >= 100) || ($photo_count >= 100)){
            DB::table('device_commands')
                ->insert([
                    'device_id' => $device->id,
                    'command' => 'CHECK',
                    'data' => '{}',
                    'created_at' => now(),
                ]);
            if($log_count >= 100) {
                DB::table('device_commands')
                    ->insert([
                        'device_id' => $device->id,
                        'command' => 'CLEAR LOG',
                        'data' => '{}',
                        'created_at' => now(),
                    ]);                
            }
            if($photo_count >= 100) {
                DB::table('device_commands')
                    ->insert([
                        'device_id' => $device->id,
                        'command' => 'CLEAR PHOTO',
                        'data' => '{}',
                        'created_at' => now(),
                    ]);        
            }

        }

        $cmd = DB::table('device_commands')
            ->where('device_id', $device->id)
            ->whereNull('executed_at')
            ->whereNull('failed_at')
            ->first();

        if (!$cmd) {
            return "OK";
        }

        DB::table('device_commands')
            ->where('id', $cmd->id)
            ->update([
                'executed_at' => now(),
                'updated_at' => now(),
            ]);

        return "C:{$cmd->id}:{$cmd->command}";
    }
    public function deviceCmdResponse(Request $request)
    {
        $sn = $request->input('SN');
        $id = preg_match('/ID=(\d+)/', $request->getContent(), $m) ? $m[1] : null;
        $return = preg_match('/Return=(\d+)/', $request->getContent(), $m2) ? $m2[1] : null;
        $cmd = preg_match('/CMD=([\s\S]+)/', $request->getContent(), $m3) ? $m3[1] : null;

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

            if(str_contains($cmd, 'DeviceName')) {
                $data = explode(chr(10), $cmd);
                preg_match('/DeviceName=(.+)/', $cmd, $m);
                $name = trim($m[1] ?? '');
                preg_match('/TransactionCount=(\d+)/', $cmd, $m2);
                $transactionCount = intval($m2[1] ?? 0);
                preg_match('/UserCount=(\d+)/', $cmd, $m3);
                $userCount = intval($m3[1] ?? 0);
                preg_match('/FPCount=(\d+)/', $cmd, $m4);
                $fpCount = intval($m4[1] ?? 0);
                preg_match('/(?<!Max)FaceCount=(\d+)/', $cmd, $faceCountMatch);
                $faceCount = intval($faceCountMatch[1] ?? 0);
                preg_match('/IPAddress=(.+)/', $cmd, $m6);
                $ip = trim($m6[1] ?? '');
                preg_match('/FWVersion=(.+)/', $cmd, $m7);
                $fw = trim($m7[1] ?? '');
                preg_match('/PushVersion=(.+)/', $cmd, $m8);
                $push = trim($m8[1] ?? '');
                DB::table('devices')
                    ->where('id', $device->id)
                    ->update([
                        'model' => $name,
                        'transaction_count' => $transactionCount,
                        'user_count' => $userCount,
                        'fp_count' => $fpCount,
                        'face_count' => $faceCount,
                        'ip_address' => $ip,
                        'fw_version' => $fw,
                        'push_version' => $push,
                        'updated_at' => now(),
                    ]);
            }
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