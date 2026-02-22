<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\Datatables;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Attendance;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    // Menampilkan daftar device
    public function index(Request $request)
    {
        $data['lable'] = "Devices";
        $data['log'] = DB::table('devices')
            ->select('id','no_sn','descripcion','online', 'model', 'ip_address', 'transaction_count', 'user_count', 'fp_count', 'face_count', 'photo_count')
            ->leftjoin('giro.supervisor_giro.lectores_adms',
                DB::raw("devices.no_sn COLLATE SQL_Latin1_General_CP1_CI_AS"),
                '=',
                DB::raw("lectores_adms.NUMERO_SERIE COLLATE SQL_Latin1_General_CP1_CI_AS")
            )
            ->orderBy('online', 'DESC')->get();
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
                sleep(2);
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
    public function Attendance()
    {
        $perPage = 15;
        $page = (int) request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        $sql = "SELECT id,sn,[table],stamp,employee_id,timestamp,status1,status2,status3,status4,status5
                FROM (
                  SELECT id,sn,[table],stamp,employee_id,timestamp,status1,status2,status3,status4,status5,
                         ROW_NUMBER() OVER (ORDER BY id DESC) AS rn
                  FROM attendances
                ) AS t
                WHERE rn BETWEEN ? AND ? ORDER BY id DESC";

        $rows = DB::select($sql, [$start + 1, $end]);

        $total = DB::table('attendances')->count();

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
