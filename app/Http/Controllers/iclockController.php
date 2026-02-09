<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $content['url'] = json_encode($request->all());
        $content['data'] = $request->getContent();
        DB::table('finger_log')->insert($content);

        try {
            $arr = preg_split('/\\r\\n|\\r|,|\\n/', $request->getContent());
            $tot = 0;

            // if ($request->input('table') == "OPERLOG") {
            //     foreach ($arr as $rey) {
            //         if (isset($rey)) {
            //             $tot++;
            //         }
            //     }
            //     return "OK: " . $tot;
            // }
            $clean = [];
            $buffer = '';

            foreach ($arr as $line) {
                if (str_starts_with(trim($line), 'FP')) {
                    // Si había un buffer previo, lo guardamos
                    if ($buffer !== '') {
                        $clean[] = $buffer;
                    }
                    $buffer = $line;
                } else {
                    // Continuación de TMP
                    $buffer .= $line;
                }
            }

            // Última línea
            if ($buffer !== '') {
                $clean[] = $buffer;
            }

            foreach ($clean as $rey) {
                if (str_starts_with(trim($rey), 'FP')) {

                    preg_match('/PIN=(\d+)/', $rey, $pin);
                    preg_match('/FID=(\d+)/', $rey, $fid);
                    preg_match('/Size=(\d+)/', $rey, $size);
                    preg_match('/Valid=(\d+)/', $rey, $valid);
                    preg_match('/TMP=([\s\S]+)/', $rey, $tmp);

                    $template = trim($tmp[1] ?? '');

                    DB::table('fingerprints')->updateOrInsert(
                        [
                            'pin' => $pin[1] ?? null,
                            'fid' => $fid[1] ?? null,
                        ],
                        [
                            'size' => $size[1] ?? 0,
                            'valid' => $valid[1] ?? 0,
                            'template' => $template,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    continue;
                }
                if (empty($rey)) continue;

                $data = explode("\t", $rey);

                $q['sn'] = $request->input('SN');
                $q['table'] = $request->input('table');
                $q['stamp'] = $request->input('Stamp');
                $q['employee_id'] = $data[0];
                $q['timestamp'] = $data[1];

                if (count($data) > 7) {
                    $q['status1'] = $this->validateAndFormatInteger($data[3] ?? null);
                    $q['status2'] = $this->validateAndFormatInteger($data[4] ?? null);
                    $q['status3'] = $this->validateAndFormatInteger($data[5] ?? null);
                    $q['status4'] = $this->validateAndFormatInteger($data[6] ?? null);
                    $q['status5'] = $this->validateAndFormatInteger($data[7] ?? null);
                } else {
                    $q['status1'] = $this->validateAndFormatInteger($data[2] ?? null);
                    $q['status2'] = $this->validateAndFormatInteger($data[3] ?? null);
                    $q['status3'] = $this->validateAndFormatInteger($data[4] ?? null);
                    $q['status4'] = $this->validateAndFormatInteger($data[5] ?? null);
                    $q['status5'] = $this->validateAndFormatInteger($data[6] ?? null);
                }

                $q['created_at'] = now();
                $q['updated_at'] = now();

                DB::table('attendances')->insert($q);
                $tot++;
            }

            return "OK: " . $tot;

        } catch (\Throwable $e) {
            $data['error'] = $e;
            DB::table('error_log')->insert($data);
            report($e);
            return "ERROR: " . $tot . "\n";
        }
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