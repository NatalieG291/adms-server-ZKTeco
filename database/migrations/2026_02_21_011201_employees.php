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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('pri')->nullable();
            $table->string('passwd')->nullable();
            $table->string('card')->nullable();
            $table->string('group')->nullable();
            $table->string('tz')->nullable();
            $table->string('verify')->nullable();
            $table->string('vice_card')->nullable();
            $table->string('start_datetime')->nullable();
            $table->string('end_datetime')->nullable();
            $table->timestamps();
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
