<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->morphs('taggable'); // Esto crea taggable_id (bigint) y taggable_type (string)
            $table->primary(['tag_id', 'taggable_id', 'taggable_type']); // Clave primaria compuesta
        });
    }

    public function down()
    {
        Schema::dropIfExists('taggables');
    }
};