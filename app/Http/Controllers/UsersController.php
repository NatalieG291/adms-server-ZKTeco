<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class UsersController extends Controller
{
    public function index()
    {
        $users = DB::Table('users')->get();


        return view('users.index', compact('users'));
    }

    public function getPermissions(Request $request){
        $permissions = DB::table('permissions')
            ->select('name', 'description')
            ->get();

        return response()->json(['permissions' => $permissions]);
    }

    public function getUserPermissions(Request $request){
        $id = DB::table('users')->select('id')->where('email', '=', $request->email)->get();
        $user = User::find($id[0]->id);

        $permissions = $user->permissions;

        return response()->json(['data' => $user]);
    }

    public function store(Request $request)
    {
        $id = DB::table('users')->select('id')->where('email', '=', $request->email)->get();
        if($id->isNotEmpty()){
            return $this->update($request);
        }
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->pass),
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        if ($request->perm) {
            $user->syncPermissions($request->perm);
        }

        return $user->load('roles', 'permissions');
    }

    public function update(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Actualizar datos básicos
        $id = DB::table('users')->select('id')->where('email', '=', $request->email)->get();
        $user = User::find($id[0]->id);
        $pass = $request->pass;
        if ($request->filled('pass')) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->pass),
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('perm')) {

            $user->syncPermissions($request->perm);
        }

        return $user->load('roles', 'permissions');
    }

    public function destroy(Request $request)
    {
        $id = DB::table('users')->select('id')->where('email', '=', $request->email)->get();
        $user = User::find($id[0]->id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}