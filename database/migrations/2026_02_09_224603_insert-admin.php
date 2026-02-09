<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $q['name'] = 'Admin';
        $q['email'] = 'admin@ossc.com.mx';
        $q['password'] = bcrypt('055c123$.');
        DB::table('users')->insert($q);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'admin@ossc.com.mx')->delete();
    }
};
