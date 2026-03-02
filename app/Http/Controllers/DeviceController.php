<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\Datatables;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Attendance;
use DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    // Menampilkan daftar device
    public function index(Request $request)
    {
        $data['lable'] = "Devices";
        // $data['log'] = DB::table('devices')
        //     ->select('id','no_sn','descripcion','online', 'model', 'ip_address', 'transaction_count', 'user_count', 'fp_count', 'face_count', 'photo_count')
        //     ->leftjoin('giro.supervisor_giro.lectores_adms',
        //         DB::raw("devices.no_sn COLLATE SQL_Latin1_General_CP1_CI_AS"),
        //         '=',
        //         DB::raw("lectores_adms.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS")
        //     )

        //     ->orderBy('online', 'DESC')->get();

        $sql = "SELECT DEVICES.ID AS id, NO_SN AS no_sn, DESCRIPCION AS descripcion, ONLINE AS online, MODEL AS model, IP_ADDRESS AS ip_address, TRANSACTION_COUNT AS transaction_count, USER_COUNT AS user_count, FP_COUNT AS fp_count, FACE_COUNT AS face_count, PHOTO_COUNT AS photo_count, 
                case 
                    when DATEDIFF(MINUTE, online, GETDATE()) > 10 THEN 'OFFLINE'
                    when command IS NULL THEN 'OK' 
                    when command like '%DATA UPDATE%' THEN 'UPLOADING'
                    when command like '%DATA QUERY%' THEN 'DOWNLOADING'
                END AS state,
                C.C_ID AS c_id
                FROM DEVICES
                LEFT JOIN GIRO.Supervisor_giro.Lectores_adms ON DEVICES.NO_SN COLLATE SQL_Latin1_General_CP1_CI_AS = lectores_adms.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS
                LEFT JOIN (
                    SELECT ID AS C_ID, DEVICE_ID, COMMAND, COMPLETED_AT, FAILED_AT, CREATED_AT, ROW_NUMBER() OVER (PARTITION BY DEVICE_ID ORDER BY CREATED_AT) AS ID 
                    FROM DEVICE_COMMANDS 
                    WHERE completed_at IS NULL and FAILED_AT IS NULL) C ON DEVICES.id = C.device_id AND C.ID = 1
                GROUP BY DEVICES.ID, NO_SN, DESCRIPCION, ONLINE, MODEL, IP_ADDRESS, TRANSACTION_COUNT, USER_COUNT, FP_COUNT, FACE_COUNT, PHOTO_COUNT, DEVICE_ID, C.C_ID, command
                order by online desc";
        $data['log'] = DB::select($sql);
        return view('devices.index',$data);
    }

    public function RestartDevice(Request $request)
    {
        $sn = $request->input('sn');
        $q['device_id'] = $sn;
        $q['command'] = 'REBOOT';
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Lector enviado para reiniciar"]);
    }

    public function ClearAdmin(Request $request)
    {
        $sn = $request->input('sn');
        $q['device_id'] = $sn;
        $q['command'] = 'CLEAR ADMIN';
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Lector enviado para limpiar admin"]);
    }

    public function ClearLog(Request $request)
    {
        $sn = $request->input('sn');
        $q['device_id'] = $sn;
        $q['command'] = 'CLEAR LOG';
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Lector enviado para limpiar log"]);
    }

    public function EnrollEmployee(Request $request)
    {
        $sn = $request->input('sn');
        $empid = $request->input('empid');
        $dedo = $request->input('dedo');
        $q['device_id'] = $sn;
        $q['command'] = 'ENROLL_FP PIN=' . $empid . "\tFID=" . $dedo . "\tRETRY=3\tOVERWRITE=1";
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Lector enviado para enrollar empleado"]);
    }

    public function SetPhotoConfig(Request $request)
    {
        $sn = $request->input('sn');
        $option = $request->input('config');
        $q['device_id'] = $sn;
        $q['command'] = 'SET OPTION CapturePic='. $option;
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Configuracion de fotos enviada al lector"]);
    }

    public function SetDuplicateTime(Request $request)
    {
        $sn = $request->input('sn');
        $minutes = $request->input('minutes');
        $q['device_id'] = $sn;
        $q['command'] = 'SET OPTION AlarmReRec='. $minutes;
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Configuracion de tiempo de duplicados enviada al lector"]);
    }

    public function Download(Request $request)
    {
        $sn = $request->input('sn');
        $all = $request->input('all');
        $many = $request->input('empids');
        if($all) {
            $q['device_id'] = $sn;
            $q['command'] = 'DATA QUERY USERINFO';
            $q['data'] = '{}';
            $q['created_at'] = now();
            DB::table('device_commands')->insert($q);
        }
        else {
             foreach($many as $empid) {
                $q['device_id'] = $sn;
                $q['command'] = 'DATA QUERY USERINFO PIN=' . $empid;
                $q['data'] = '{}';
                $q['created_at'] = now();
                DB::table('device_commands')->insert($q);
            }
        }
        return response()->json(['message' => "Datos de empleados solicitados al lector"]);
    }

    public function Upload(Request $request)
    {
        $sn = $request->input('sn');
        $all = $request->input('all');
        $many = $request->input('empids');
        $fp = $request->input('fp');
        $face = $request->input('face');
        $photo = $request->input('photo');
        if($all) {
            $employees = DB::table('employees')->get();
            foreach($employees as $employee) {
                $q['device_id'] = $sn;
                $q['command'] = "DATA UPDATE USERINFO\tPIN=$employee->employee_id\tName=$employee->name\tPri=$employee->pri\tPasswd=$employee->passwd\tCard=$employee->card\tGrp=1\tVerify=$employee->verify";
                $q['data'] = '{}';
                $q['created_at'] = now();
                DB::table('device_commands')->insert($q);

                if($fp) {
                    $fpdata = DB::table('fingerprints')->where('pin', $employee->employee_id)->get();
                    foreach($fpdata as $fingerprint) {
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE FINGERTMP PIN=$fingerprint->pin\tFID=$fingerprint->fid\tSize=$fingerprint->size\tValid=$fingerprint->valid\tTMP=$fingerprint->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
                if($face) {
                    $fcdata = DB::table('faces')->where('pin', $employee->employee_id)->get();
                    foreach($fcdata as $face) {
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE FACE\tPIN=$face->pin\tFID=$face->fid\tSize=$face->size\tValid=1\tTMP=$face->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
                if($photo) {
                    $pdata = DB::table('emp_photos')->where('employee_id', $employee->employee_id)->first();
                    if($pdata) {
                        $base64 = base64_encode(Storage::disk('public')->get("userpic/$employee->employee_id.jpg"));
                        $size = $pdata->size;
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE USERPIC\tPIN=$employee->employee_id\tSize=$size\tContent=$base64";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
            }
        }
        else {
            foreach($many as $empid) {
                $employee = DB::table('employees')->where('employee_id', $empid)->first();
                if($employee) {
                    $q['device_id'] = $sn;
                    $q['command'] = "DATA UPDATE USERINFO\tPIN=$employee->employee_id\tName=$employee->name\tPri=$employee->pri\tPasswd=$employee->passwd\tCard=$employee->card\tGrp=1\tVerify=$employee->verify";
                    $q['data'] = '{}';
                    $q['created_at'] = now();
                    DB::table('device_commands')->insert($q);
                }
                if($fp) {
                    $fpdata = DB::table('fingerprints')->where('pin', $employee->employee_id)->get();
                    foreach($fpdata as $fingerprint) {
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE FINGERTMP PIN=$fingerprint->pin\tFID=$fingerprint->fid\tSize=$fingerprint->size\tValid=$fingerprint->valid\tTMP=$fingerprint->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
                if($face) {
                    $fcdata = DB::table('faces')->where('pin', $employee->employee_id)->get();
                    foreach($fcdata as $face) {
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE FACE\tPIN=$face->pin\tFID=$face->fid\tSize=$face->size\tValid=1\tTMP=$face->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
                if($photo) {
                    $pdata = DB::table('emp_photos')->where('employee_id', $employee->employee_id)->first();
                    if($pdata) {
                        $base64 = base64_encode(Storage::disk('public')->get("userpic/$employee->employee_id.jpg"));
                        $size = $pdata->size;
                        $q['device_id'] = $sn;
                        $q['command'] = "DATA UPDATE USERPIC\tPIN=$employee->employee_id\tSize=$size\tContent=$base64";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
            }
        }
        return response()->json(['message' => "Datos de empleados enviados al lector"]);
    }

    public function deleteData (Request $request)
    {
        $sn = $request->input('sn');
        $q['device_id'] = $sn;
        $q['command'] = 'CLEAR DATA';
        $q['data'] = '{}';
        $q['created_at'] = now();
        DB::table('device_commands')->insert($q);
        return response()->json(['message' => "Datos de empleados eliminados del lector"]);
    }

    public function DeviceLog(Request $request)
    {
        $perPage = 15;
        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        $sql = "SELECT id,data,url
                FROM (
                  SELECT id,data,url,
                         ROW_NUMBER() OVER (ORDER BY id DESC) AS rn
                  FROM device_log
                ) AS t
                WHERE rn BETWEEN ? AND ? ORDER BY id DESC";
        $rows = DB::select($sql, [$start + 1, $end]);
        $total = DB::table('device_log')->count();
        $logs = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        
        return view('devices.log',compact('logs'));
    }
    
    public function FingerLog(Request $request)
    {
        $perPage = 15;
        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;   
        $sql = "SELECT id,url, data
                FROM (
                  SELECT id,url, data,
                         ROW_NUMBER() OVER (ORDER BY id DESC) AS rn
                  FROM finger_log
                ) AS t
                WHERE rn BETWEEN ? AND ? ORDER BY id DESC";
        $rows = DB::select($sql, [$start + 1, $end]);
        $total = DB::table('finger_log')->count();
        $logs = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        return view('devices.finger',compact('logs'));
    }
    public function Attendance(Request $request)
    {
        $perPage = 15;

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        $selectCols = "id, descripcion, employee_id, timestamp, 
            CASE 
                WHEN status1 = 1 THEN 'Huella' 
                WHEN status1 = 3 THEN 'Contraseña'
                WHEN status1 = 4 THEN 'Tarjeta'
                WHEN status1 = 5 THEN 'Huella/Contraseña'
                WHEN status1 = 6 THEN 'Huella/Tarjeta'
                WHEN status1 = 7 THEN 'Tarjeta/Contraseña'
                WHEN status1 = 8 THEN 'ID de usuario y huella'
                WHEN status1 = 9 THEN 'Huella y Contraseña'
                WHEN status1 = 10 THEN 'Huella y tarjeta'
                WHEN status1 = 11 THEN 'Contraseña y tarjeta'
                WHEN status1 = 12 THEN 'Huella, contraseña y tarjeta'
                WHEN status1 = 13 THEN 'ID de usuario, huella y contraseña'
                WHEN status1 = 14 THEN 'Huella y tarjeta / ID de usuario'
                WHEN status1 = 15 THEN 'Rostro' 
                WHEN status1 = 16 THEN 'Rostro y huella'
                WHEN status1 = 17 THEN 'Rostro y contraseña'
                WHEN status1 = 18 THEN 'Rostro y tarjeta'
                WHEN status1 = 19 THEN 'Rostro, huella y tarjeta'
                WHEN status1 = 20 THEN 'Rostro, huella y contraseña'
                ELSE CAST(STATUS1 AS VARCHAR) END AS status1";
        $whereSql = '';
        $bindings = [];

        if ($startDate || $endDate) {
            $startDatetime = $startDate ? ($startDate . ' ' . ($startTime ?: '00:00:00')) : null;
            $endDatetime = $endDate ? ($endDate . ' ' . ($endTime ?: '23:59:59')) : null;
            if ($startDatetime && $endDatetime) {
                $whereSql = 'WHERE timestamp BETWEEN ? AND ?';
                $bindings = [$startDatetime, $endDatetime];
            } elseif ($startDatetime) {
                $whereSql = 'WHERE timestamp >= ?';
                $bindings = [$startDatetime];
            } elseif ($endDatetime) {
                $whereSql = 'WHERE timestamp <= ?';
                $bindings = [$endDatetime];
            }
        } else {
            if ($startTime && $endTime) {
                $whereSql = 'WHERE CONVERT(time, timestamp) BETWEEN ? AND ?';
                $bindings = [$startTime, $endTime];
            }
        }

        if ($request->get('export')) {
            $sqlExport = "SELECT l.descripcion, employee_id, timestamp,
            CASE 
                WHEN status1 = 1 THEN 'Huella' 
                WHEN status1 = 3 THEN 'Contraseña'
                WHEN status1 = 4 THEN 'Tarjeta'
                WHEN status1 = 5 THEN 'Huella/Contraseña'
                WHEN status1 = 6 THEN 'Huella/Tarjeta'
                WHEN status1 = 7 THEN 'Tarjeta/Contraseña'
                WHEN status1 = 8 THEN 'ID de usuario y huella'
                WHEN status1 = 9 THEN 'Huella y Contraseña'
                WHEN status1 = 10 THEN 'Huella y tarjeta'
                WHEN status1 = 11 THEN 'Contraseña y tarjeta'
                WHEN status1 = 12 THEN 'Huella, contraseña y tarjeta'
                WHEN status1 = 13 THEN 'ID de usuario, huella y contraseña'
                WHEN status1 = 14 THEN 'Huella y tarjeta / ID de usuario'
                WHEN status1 = 15 THEN 'Rostro' 
                WHEN status1 = 16 THEN 'Rostro y huella'
                WHEN status1 = 17 THEN 'Rostro y contraseña'
                WHEN status1 = 18 THEN 'Rostro y tarjeta'
                WHEN status1 = 19 THEN 'Rostro, huella y tarjeta'
                WHEN status1 = 20 THEN 'Rostro, huella y contraseña'
                ELSE CAST(STATUS1 AS VARCHAR) END AS status1 
            FROM attendances a LEFT JOIN GIRO.Supervisor_giro.Lectores_adms l ON a.SN COLLATE SQL_Latin1_General_CP1_CI_AS = l.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS" . ($whereSql ? ' ' . $whereSql : '') . " ORDER BY id DESC";
            $rows = DB::select($sqlExport, $bindings);
            $filename = 'attendances_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['lector', 'Clave Empleado', 'Fecha y hora', 'Metodo checada']);
                foreach ($rows as $r) {
                    $ts = isset($r->timestamp) ? Carbon::parse($r->timestamp)->format('Y-m-d H:i:s') : '';
                    fputcsv($out, [
                        $r->descripcion,
                        $r->employee_id,
                        $ts,
                        $r->status1,
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        $sql = "SELECT id, descripcion, employee_id, timestamp, status1 FROM (SELECT $selectCols, ROW_NUMBER() OVER (ORDER BY id DESC) AS rn 
        FROM attendances a LEFT JOIN GIRO.Supervisor_giro.Lectores_adms l ON a.SN COLLATE SQL_Latin1_General_CP1_CI_AS = l.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS" . ($whereSql ? ' ' . $whereSql : '') . ") AS t WHERE rn BETWEEN ? AND ? ORDER BY id DESC";

        $rows = DB::select($sql, array_merge($bindings, [$start + 1, $end]));

        $countSql = "SELECT COUNT(*) AS cnt FROM attendances " . ($whereSql ? ' ' . $whereSql : '');
        $countRow = DB::selectOne($countSql, $bindings);
        $total = $countRow ? (int) $countRow->cnt : 0;

        $attendances = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('devices.attendance', compact('attendances'));
    }

    public function attphoto()
    {
        $perPage = 15;
        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;
        
        $sql = "SELECT id,employee_id,timestamp,filename,size,sn
                FROM (
                  SELECT id,employee_id,timestamp,filename,size,sn,
                         ROW_NUMBER() OVER (ORDER BY timestamp DESC) AS rn
                  FROM attphoto
                ) AS t
                WHERE rn BETWEEN ? AND ? ORDER BY timestamp DESC";
        $rows = DB::select($sql, [$start + 1, $end]);
        $total = DB::table('attphoto')->count();
        $photos = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        return view('devices.attphoto', compact('photos'));
    }

    // // Menampilkan form tambah device
    // public function create()
    // {
    //     return view('devices.create');
    // }

    // // Menyimpan device baru ke database
    // public function store(Request $request)
    // {
    //     $device = new Device();
    //     $device->nama = $request->input('nama');
    //     $device->no_sn = $request->input('no_sn');
    //     $device->lokasi = $request->input('lokasi');
    //     $device->save();

    //     return redirect()->route('devices.index')->with('success', 'Device berhasil ditambahkan!');
    // }

    // // Menampilkan detail device
    // public function show($id)
    // {
    //     $device = Device::find($id);
    //     return view('devices.show', compact('device'));
    // }

    // // Menampilkan form edit device
    // public function edit($id)
    // {
    //     $device = Device::find($id);
    //     return view('devices.edit', compact('device'));
    // }

    // // Mengupdate device ke database
    // public function update(Request $request, $id)
    // {
    //     $device = Device::find($id);
    //     $device->nama = $request->input('nama');
    //     $device->no_sn = $request->input('no_sn');
    //     $device->lokasi = $request->input('lokasi');
    //     $device->save();

    //     return redirect()->route('devices.index')->with('success', 'Device berhasil diupdate!');
    // }

    // // Menghapus device dari database
    // public function destroy($id)
    // {
    //     $device = Device::find($id);
    //     $device->delete();

    //     return redirect()->route('devices.index')->with('success', 'Device berhasil dihapus!');
    // }
}
