<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Añade esta línea

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Añadir la columna permitiendo valores nulos temporalmente
            $table->foreignId('account_id')
                  ->after('category_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');
        });

        // Verifica si hay cuentas existentes
        if (Schema::hasTable('accounts') && DB::table('accounts')->exists()) {
            // Actualizar registros existentes con el primer account_id disponible
            DB::table('transactions')->update([
                'account_id' => DB::table('accounts')->first()->id
            ]);
        }

        // Hacer la columna no nula después de asignar valores
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};