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
        Schema::table('devices', function (Blueprint $table) {
            $table->string('model')->nullable();
            $table->string('fw_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('transaction_count')->nullable();
            $table->string('user_count')->nullable();
            $table->string('fp_count')->nullable();
            $table->string('face_count')->nullable();
            $table->string('push_version')->nullable();
            $table->string('photo_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
