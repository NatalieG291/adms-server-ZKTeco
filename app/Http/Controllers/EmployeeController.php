<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\Datatables;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Attendance;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

        $params = [];
        $filterCondition = '';
        if($busqueda !== '') {
            $filterCondition = "WHERE (employee_id LIKE ? OR name LIKE ?)";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }

        $sql = "SELECT t.id,t.employee_id,name,
                case when pri = 0 then 'Empleado' 
                    when pri = 2 then 'Registro' 
                    when pri = 6 then 'Administrador del sistema' 
                    when pri = 10 then 'Definido por el usuario' 
                    when pri = 14 then 'Superadministrador'
                    end as pri,pri as pri_id,passwd,card,verify as verify_id,t.updated_at,
                case when verify = -1 then 'Definido por el usuario' 
                    when verify = 0 then 'Cualquiera' 
                    when verify = 1 then 'Huella dactilar' 
                    when verify = 2 then 'Solo ID de usuario' 
                    when verify = 3 then 'Contraseña' 
                    when verify = 4 then 'Solo tarjeta' 
                    when verify = 5 then 'Huella/Contraseña' 
                    when verify = 6 then 'Huella/Tarjeta' 
                    when verify = 7 then 'Tarjeta/Contraseña'
                    when verify = 8 then 'ID de usuario y huella' 
                    when verify = 9 then 'Huella y contraseña' 
                    when verify = 10 then 'Huella y tarjeta' 
                    when verify = 11 then 'Contraseña y tarjeta' 
                    when verify = 12 then 'Huella, contraseña y tarjeta' 
                    when verify = 13 then 'ID de usuario, huella y contraseña'
                    when verify = 14 then 'Huella y tarjeta / ID de usuario'
                    when verify = 15 then 'Solo rostro'
                    when verify = 16 then 'Rostro y huella'
                    when verify = 17 then 'Rostro y contraseña'
                    when verify = 18 then 'Rostro y tarjeta'
                    when verify = 19 then 'Rostro, huella y tarjeta'
                    when verify = 20 then 'Rostro, huella y contraseña'
                    end as verify,
                emp_photos.photo,
                finger_count.total
                FROM (
                  SELECT id,employee_id,name,pri,pri as pri_id,passwd,card,[group],tz,verify,vice_card,start_datetime,end_datetime,updated_at,
                         ROW_NUMBER() OVER (ORDER BY id DESC) AS rn
                  FROM employees
                  ".$filterCondition."
                ) AS t
                LEFT JOIN emp_photos ON T.employee_id = emp_photos.employee_id
                LEFT JOIN (select pin, COUNT(pin) as total from fingerprints group by pin) as finger_count ON T.employee_id = finger_count.pin
                WHERE rn BETWEEN ? AND ? ORDER BY id DESC";
        $params[] = $start + 1;
        $params[] = $end;
        
        $rows = DB::select($sql, $params);

        $total = DB::table('employees')
                    ->where('employee_id', 'like', "%$busqueda%")
                    ->orWhere('name', 'like', "%$busqueda%")
                    ->count();

        $employees = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        $devices = DB::table('devices')
            ->select('id','no_sn','descripcion')
            ->leftjoin('giro.supervisor_giro.lectores_adms',
                DB::raw("devices.no_sn COLLATE SQL_Latin1_General_CP1_CI_AS"),
                '=',
                DB::raw("lectores_adms.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS")
            )
            ->orderBy('online', 'DESC')->get();

        return view('devices.employees', compact('employees', 'devices'));
    }

    public function fingerprints(Request $request){
        $employeeId = $request->input('pin');
        $fingers = DB::table('fingerprints')->select('fid')->where('pin', $employeeId)->pluck('fid');

        return response()->json(['fids' => $fingers]);
    }

    public function UploadPhoto(Request $request)
    {
        $auditData = [
            'user_id' => auth()->check() ? auth()->id() : null,
            'action' => 'Upload Employee Photo',
            'description' => 'Employee ID: ' . $request->input('employee_id'),
        ];
        DB::table('audit_logs')->insert($auditData);

        $employeeId = $request->input('employee_id');
        $base64 = $request->input('base64');
        $size = $request->input('size');

        if ($base64) {
            $filename = "$employeeId.jpg";
            $path = storage_path('app/public/userpic/' . $filename);

            // Remove base64 header if present
            if (strpos($base64, ',') !== false) {
                $base64 = explode(',', $base64, 2)[1];
            }

            $imageData = base64_decode($base64);
            if ($imageData !== false) {
                file_put_contents($path, $imageData);

                DB::table('emp_photos')->updateOrInsert(
                    ['employee_id' => $employeeId],
                    ['photo' => $base64, 'size' => $size, 'updated_at' => now()]
                );

                return response()->json(['message' => 'Foto subida exitosamente', 'success' => true]);
            }
        }

        return response()->json(['message' => 'Carga de foto inválida'], 400);
    }

    public function ListEmployees(Request $request)
    {
        $employees = DB::table('employees')
            ->select('employee_id', 'name')
            ->get();

        return response()->json(['employees' => $employees]);
    }

    public function EditEmployeeData(Request $request)
    {
        $auditData = [
            'user_id' => auth()->check() ? auth()->id() : null,
            'action' => 'Edit Employee Data',
            'description' => 'Employee ID: ' . $request->input('empid') . 'name: ' . $request->input('name') . 'pri: ' . $request->input('pri') . 'card: ' . $request->input('card') . 'verify: ' . $request->input('verify') . 'passwd: ' . $request->input('passwd'),
        ];
        DB::table('audit_logs')->insert($auditData);
        $employeeId = $request->input('empid');
        $name = $request->input('name');
        if($name === null) $name = '';
        $pri = $request->input('pri');
        $passwd = $request->input('passwd');
        $card = $request->input('card');
        $verify = $request->input('verify');
        $send = $request->input('send');
        $devices = $request->input('devices');

        $var = DB::table('employees')
            ->where('employee_id', $employeeId)
            ->update([
                'name' => $name,
                'pri' => $pri,
                'passwd' => $passwd,
                'card' => $card,
                'verify' => $verify,
                'updated_at' => now(),
            ]);

        if($send) {
            if($devices == 'all') {
                $devicesToUpdate = DB::table('devices')->select('id')->pluck('id');
                foreach($devicesToUpdate as $deviceId) {
                    DB::table('device_commands')
                    ->insert([
                        'device_id' => $deviceId,
                        'command' => "DATA UPDATE USERINFO\tPIN=$employeeId\tName=$name\tPri=$pri\tPasswd=$passwd\tCard=$card\tGrp=1\tVerify=$verify",
                        'data' => '{}',
                        'created_at' => now(),
                    ]);

                    $fpdata = DB::table('fingerprints')->where('pin', $employeeId)->get();
                    foreach($fpdata as $fingerprint) {
                        $q['device_id'] = $deviceId;
                        $q['command'] = "DATA UPDATE FINGERTMP PIN=$fingerprint->pin\tFID=$fingerprint->fid\tSize=$fingerprint->size\tValid=$fingerprint->valid\tTMP=$fingerprint->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }

                    $fcdata = DB::table('faces')->where('pin', $employeeId)->get();
                    foreach($fcdata as $face) {
                        $q['device_id'] = $deviceId;
                        $q['command'] = "DATA UPDATE FACE\tPIN=$face->pin\tFID=$face->fid\tSize=$face->size\tValid=1\tTMP=$face->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }

                    $pdata = DB::table('emp_photos')->where('employee_id', $employeeId)->first();
                    if($pdata) {
                        $base64 = base64_encode(Storage::disk('public')->get("userpic/$employeeId.jpg"));
                        $size = $pdata->size;
                        $q['device_id'] = $deviceId;
                        $q['command'] = "DATA UPDATE USERPIC\tPIN=$employeeId\tSize=$size\tContent=$base64";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                }
            }
            else {
                DB::table('device_commands')
                    ->insert([
                        'device_id' => $devices,
                        'command' => "DATA UPDATE USERINFO\tPIN=$employeeId\tName=$name\tPri=$pri\tPasswd=$passwd\tCard=$card\tGrp=1\tVerify=$verify",
                        'data' => '{}',
                        'created_at' => now(),
                    ]);

                    $fpdata = DB::table('fingerprints')->where('pin', $employeeId)->get();
                    foreach($fpdata as $fingerprint) {
                        $q['device_id'] = $devices;
                        $q['command'] = "DATA UPDATE FINGERTMP PIN=$fingerprint->pin\tFID=$fingerprint->fid\tSize=$fingerprint->size\tValid=$fingerprint->valid\tTMP=$fingerprint->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
                    
                    $fcdata = DB::table('faces')->where('pin', $employeeId)->get();
                    foreach($fcdata as $face) {
                        $q['device_id'] = $devices;
                        $q['command'] = "DATA UPDATE FACE\tPIN=$face->pin\tFID=$face->fid\tSize=$face->size\tValid=1\tTMP=$face->template";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }

                    $pdata = DB::table('emp_photos')->where('employee_id', $employeeId)->first();
                    if($pdata) {
                        $base64 = base64_encode(Storage::disk('public')->get("userpic/$employeeId.jpg"));
                        $size = $pdata->size;
                        $q['device_id'] = $devices;
                        $q['command'] = "DATA UPDATE USERPIC\tPIN=$employeeId\tSize=$size\tContent=$base64";
                        $q['data'] = '{}';
                        $q['created_at'] = now();
                        DB::table('device_commands')->insert($q);
                    }
            }
        }

        return response()->json(['message' => 'Datos del empleado actualizados exitosamente']);
    }
}