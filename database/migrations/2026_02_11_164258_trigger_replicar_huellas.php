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
        DB::unprepared("
            CREATE TRIGGER [dbo].[replicar_huellas]
            ON [adms].[dbo].[attendances]
            AFTER INSERT
            AS
            BEGIN
                SET NOCOUNT ON;

                BEGIN TRY
                    BEGIN TRANSACTION
                        ---------------------------------------------------------
                        -- 1. Insertar lector si no existe 
                        ---------------------------------------------------------
                        INSERT INTO GIRO.Supervisor_giro.Lectores_adms (NUMERO_SERIE)
                        SELECT DISTINCT LTRIM(RTRIM(i.SN))
                        FROM inserted i
                        WHERE NOT EXISTS (
                            SELECT 1
                            FROM GIRO.Supervisor_giro.Lectores_adms l
                            WHERE LTRIM(RTRIM(l.NUMERO_SERIE)) = LTRIM(RTRIM(i.SN))
                        );

                        ---------------------------------------------------------
                        -- 2. Insertar registros en BitacoraRegistros
                        ---------------------------------------------------------
                        INSERT INTO GIRO.Supervisor_giro.BitacoraRegistros
                            (CLAVE, FECHA, FECHA_LECTURA, LECTOR)
                        SELECT
                            i.EMPLOYEE_ID,
                            i.timestamp,
                            CONVERT(DATETIME, CONVERT(VARCHAR(20), i.CREATED_AT, 120)),
                            l.CLAVE
                        FROM inserted i
                        INNER JOIN GIRO.Supervisor_giro.Lectores_adms l
                            ON LTRIM(RTRIM(l.NUMERO_SERIE)) = LTRIM(RTRIM(i.SN));
                    COMMIT TRANSACTION
                END TRY
                BEGIN CATCH
                    ---------------------------------------------------------
                    -- 3. Registro de errores
                    ---------------------------------------------------------
                    INSERT INTO dbo.trigger_log (mensaje)
                    VALUES (
                        'Error en trigger replicar_huellas: ' +
                        ERROR_MESSAGE()
                    );
                END CATCH
            END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS dbo.replicar_huellas
        ");
    }
};
