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
        Schema::create('subtareas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('idTarea')->constrained('tareas')->onDelete('cascade');

            $table->string('titulo');
            $table->text('descripcion')->nullable(); // Opcional según tu diagrama
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtareas');
    }
};
