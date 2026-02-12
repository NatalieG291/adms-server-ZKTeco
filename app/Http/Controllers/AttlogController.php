<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use DateTime;

class AttlogController extends Controller
{
    public function index()
    {
        // 1. Obtener los registros crudos desde finger_log
        $logs = DB::table('finger_log')->select('id', 'data')->get();

        $attlogs = [];
        $deleted = DB::table('attlog')->delete();

        foreach ($logs as $log) {
            // 2. Separar por líneas
            $lineas = preg_split('/\r\n|\r|\n/', $log->data);

            foreach ($lineas as $linea) {
                $linea = trim($linea);

                // 3. Filtrar solo ATTLOG (excluir FP, OPERLOG, DeviceInfo, etc.)
                if ($linea === '') continue;
                if (preg_match('/^(FP|FINGERTMP|OPERLOG|OPLOG|~DeviceName)/i', $linea)) continue;

                // 4. Separar por espacios o tabs
                $cols = preg_split('/\s+/', $linea);

                // Validar que tenga al menos 3 columnas
                if (count($cols) < 3) continue;

                $attlogs[] = [
                    'id'          => $log->id,
                    'employee_id' => $cols[0],
                    'fecha'       => $cols[1],
                    'hora'        => $cols[2],
                    'status1'     => $cols[3] ?? null,
                    'status2'     => $cols[4] ?? null,
                    'status3'     => $cols[5] ?? null,
                    'status4'     => $cols[6] ?? null,
                    'status5'     => $cols[7] ?? null,
                ];

                $q['employee_id'] = $cols[0];
                $q['timestamp'] = new DateTime($cols[1].' '.$cols[2]);
                $q['status1'] = $cols[3];
                $q['status2'] = $cols[4];
                $q['status3'] = $cols[5];
                $q['status4'] = $cols[6];
                $q['status5'] = $cols[7];
                DB::table('attlog')->insert($q);
            }
        }

        return view('attlog.index', compact('attlogs'));
    }
}