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
    // Guardar crudo para debugging
    DB::table('finger_log')->insert([
        'url'  => json_encode($request->all()),
        'data' => $request->getContent(),
    ]);

    try {
        $raw = trim($request->getContent());

        // Primero detectamos si hay huellas (FP ...)
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $clean = [];
        $buffer = '';

        foreach ($lines as $line) {
            $trim = trim($line);

            // Inicio de una huella
            if (str_starts_with($trim, 'FP')) {
                if ($buffer !== '') {
                    $clean[] = $buffer;
                }
                $buffer = $trim;
            } else {
                // Continuación de TMP
                if ($buffer !== '') {
                    $buffer .= $trim;
                }
            }
        }

        if ($buffer !== '') {
            $clean[] = $buffer;
        }

        $tot = 0;

        // Procesar huellas primero
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

        // Ahora procesamos ATTLOG
        // Si no hay tabs ni saltos → viene todo en una sola línea
        if (!str_contains($raw, "\t") && !str_contains($raw, "\n")) {

            // Separar por espacios múltiples
            $tokens = preg_split('/\s+/', $raw);

            // Cada registro ATTLOG tiene 10 campos
            $records = array_chunk($tokens, 10);

            foreach ($records as $data) {
                if (count($data) < 3) continue;

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
                $tot++;
            }

            return "OK: " . $tot;
        }

        // Si viene en formato normal (con tabs)
        $rows = preg_split('/\r\n|\r|\n/', $raw);

        foreach ($rows as $row) {
            if (empty(trim($row))) continue;
            if (str_starts_with(trim($row), 'FP')) continue;

            $data = explode("\t", $row);
            if (count($data) < 2) continue;

            $q['sn']         = $request->input('SN');
            $q['table']      = $request->input('table');
            $q['stamp']      = $request->input('Stamp');
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
            $tot++;
        }

        return "OK: " . $tot;

    } catch (\Throwable $e) {
        DB::table('error_log')->insert(['error' => $e->getMessage()]);
        report($e);
        return "ERROR";
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