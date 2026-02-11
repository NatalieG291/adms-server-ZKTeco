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
        Schema::connection('giro')->create('Supervisor_giro.Lectores_adms', function (blueprint $table) {
            $table->increments('CLAVE');
            $table->string('NUMERO_SERIE');
            $table->string('DESCRIPCION');

            $table->unique(['CLAVE', 'NUMERO_SERIE']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('giro')->dropIfExists('Supervisor_giro.Lectores_adms');
    }
};
