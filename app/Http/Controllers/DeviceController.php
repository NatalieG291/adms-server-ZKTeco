<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\Datatables;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Attendance;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceController extends Controller
{
    // Menampilkan daftar device
    public function index(Request $request)
    {
        $data['lable'] = "Devices";
        $data['log'] = DB::table('devices')->select('id','no_sn','online')->orderBy('online', 'DESC')->get();
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
