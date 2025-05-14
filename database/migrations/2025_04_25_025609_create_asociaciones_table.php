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
        Schema::create('asociaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('municipalidad_id')->constrained('municipalidad')->onDelete('cascade');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Añadir campo asociacion_id a la tabla emprendedores
        Schema::table('emprendedores', function (Blueprint $table) {
            $table->foreignId('asociacion_id')->nullable()->constrained('asociaciones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emprendedores', function (Blueprint $table) {
            $table->dropForeign(['asociacion_id']);
            $table->dropColumn('asociacion_id');
        });
        
        Schema::dropIfExists('asociaciones');
    }
};