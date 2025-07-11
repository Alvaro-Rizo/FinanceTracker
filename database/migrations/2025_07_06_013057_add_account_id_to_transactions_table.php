<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_account_id_to_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Verifica si la columna no existe antes de agregarla
            if (!Schema::hasColumn('transactions', 'account_id')) {
                $table->foreignId('account_id')
                    ->nullable()
                    ->constrained() // Automatically creates the foreign key constraint
                    ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['account_id']);
            // Luego eliminar la columna
            $table->dropColumn('account_id');
        });
    }
};