<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::create(['name' => 'view-attendance', 'description' => 'Ver asistencia']);
        Permission::create(['name' => 'view-attendance-photos', 'description' => 'Ver fotos de asistencia']);
        Permission::create(['name' => 'view-employees', 'description' => 'Ver empleados']);
        $admin = User::role('admin')->first();
        $admin->givePermissionTo(Permission::all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
