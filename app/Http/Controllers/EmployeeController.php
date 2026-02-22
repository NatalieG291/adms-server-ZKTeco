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

        $sql = "SELECT t.id,t.employee_id,name,
                case when pri = 0 then 'Employee' 
                    when pri = 2 then 'Register' 
                    when pri = 6 then 'System Administrator' 
                    when pri = 10 then 'User Defined' 
                    when pri = 14 then 'Super Admin'
                    end as pri,pri as pri_id,passwd,card,verify as verify_id,t.updated_at,
                case when verify = -1 then 'User Defined' 
                    when verify = 0 then 'Any' 
                    when verify = 1 then 'Fingerprint' 
                    when verify = 2 then 'UserID Only' 
                    when verify = 3 then 'Password' 
                    when verify = 4 then 'Card Only' 
                    when verify = 5 then 'Fingerprint/Password' 
                    when verify = 6 then 'Fingerprint/Card' 
                    when verify = 7 then 'Card/Password'
                    when verify = 8 then 'UserID & Fingerprint' 
                    when verify = 9 then 'Fingerprint & Password' 
                    when verify = 10 then 'Fingerprint & Card' 
                    when verify = 11 then 'Password & Card' 
                    when verify = 12 then 'Fingerprint & Password & Card' 
                    when verify = 13 then 'UserID & Fingerprint & Password'
                    when verify = 14 then 'Fingerprint & Card / UserID'
                    when verify = 15 then 'Face Only'
                    when verify = 16 then 'Face & Fingerprint'
                    when verify = 17 then 'Face & Password'
                    when verify = 18 then 'Face & Card'
                    when verify = 19 then 'Face & Fingerprint & Card'
                    when verify = 20 then 'Face & Fingerprint & Password'
                    end as verify,
                emp_photos.photo
                FROM (
                  SELECT id,employee_id,name,pri,pri as pri_id,passwd,card,[group],tz,verify,vice_card,start_datetime,end_datetime,updated_at,
                         ROW_NUMBER() OVER (ORDER BY id DESC) AS rn
                  FROM employees
                ) AS t
                LEFT JOIN emp_photos ON T.employee_id = emp_photos.employee_id
                WHERE rn BETWEEN ? AND ? ORDER BY id DESC";

        $rows = DB::select($sql, [$start + 1, $end]);

        $total = DB::table('employees')->count();

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

    public function UploadPhoto(Request $request)
    {
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

                return response()->json(['message' => 'Photo uploaded successfully', 'success' => true]);
            }
        }

        return response()->json(['message' => 'Invalid photo upload'], 400);
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
        $employeeId = $request->input('empid');
        $name = $request->input('name');
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

        return response()->json(['message' => 'Employee data updated successfully']);
    }
}