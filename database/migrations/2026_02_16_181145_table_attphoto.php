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
        Schema::create('attphoto', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->dateTime('timestamp');
            $table->string('filename');
            $table->integer('size');
            $table->string('sn');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attphoto');
    }
};
