<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('TransTimes')->nullable();
            $table->string('Delay')->nullable();
            $table->string('RealTime')->nullable();
            $table->string('TransInterval')->nullable();
        });
    }

    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('TransTimes');
            $table->dropColumn('Delay');
            $table->dropColumn('RealTime');
            $table->dropColumn('TransInterval');
        });
    }
};
