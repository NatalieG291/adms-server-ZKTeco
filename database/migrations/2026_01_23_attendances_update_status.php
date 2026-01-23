<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('status1')->nullable()->change();
            $table->integer('status2')->nullable()->change();
            $table->integer('status3')->nullable()->change();
            $table->integer('status4')->nullable()->change();
            $table->integer('status5')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('status1')->nullable()->change();
            $table->boolean('status2')->nullable()->change();
            $table->boolean('status3')->nullable()->change();
            $table->boolean('status4')->nullable()->change();
            $table->boolean('status5')->nullable()->change();
        });
    }
};