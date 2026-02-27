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

        Permission::create(['name' => 'device-reboot']);
        Permission::create(['name' => 'device-clear-admin']);
        Permission::create(['name' => 'device-clear-data']);
        Permission::create(['name' => 'device-clear-log']);
        Permission::create(['name' => 'device-capture-setting']);
        Permission::create(['name' => 'device-punch-period']);
        Permission::create(['name' => 'device-remote-enroll']);
        Permission::create(['name' => 'device-download-data']);
        Permission::create(['name' => 'device-upload-data']);

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
