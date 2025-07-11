<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

   public function up()
{
    Schema::table('users', function (Blueprint $table) {
     
        $table->timestamp('name_updated_at')->nullable();
        $table->timestamp('preferences_updated_at')->nullable();
    });
}

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Verificar y añadir cada columna solo si no existe
            if (!Schema::hasColumn('users', 'currency')) {
                $table->string('currency', 3)->default('NIO')->after('email');
            }
            
            if (!Schema::hasColumn('users', 'dark_mode')) {
                $table->boolean('dark_mode')->default(false)->after('currency');
            }
            
            if (!Schema::hasColumn('users', 'name_updated_at')) {
                $table->timestamp('name_updated_at')->nullable()->after('dark_mode');
            }
            
            if (!Schema::hasColumn('users', 'preferences_updated_at')) {
                $table->timestamp('preferences_updated_at')->nullable()->after('name_updated_at');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar solo las columnas si existen
            $columnsToDrop = ['currency', 'dark_mode', 'name_updated_at', 'preferences_updated_at'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};