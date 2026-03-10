<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'device-reboot', 'description' => 'Reiniciar']);
        Permission::create(['name' => 'device-clear-admin', 'description' => 'Borrar administrador']);
        Permission::create(['name' => 'device-clear-data', 'description' => 'Borrar datos']);
        Permission::create(['name' => 'device-clear-log', 'description' => 'Borrar registro']);
        Permission::create(['name' => 'device-capture-setting', 'description' => 'Configuracion de fotos']);
        Permission::create(['name' => 'device-punch-period', 'description' => 'Periodo de acceso duplicado']);
        Permission::create(['name' => 'device-remote-enroll', 'description' => 'Enrolamiento remoto']);
        Permission::create(['name' => 'device-download-data', 'description' => 'Descargar datos de usuarios']);
        Permission::create(['name' => 'device-upload-data', 'description' => 'Subir datos de usuarios']);
        Permission::create(['name' => 'device-delete-employee', 'description' => 'Eliminar empleados del lector']);
        Permission::create(['name' => 'device-change-config', 'description' => 'Cambiar configuracion del lector']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@ossc.com.mx',
            'password' => bcrypt('055c123$.'),
        ]);
        $adminUser->assignRole('admin');

    }
}
