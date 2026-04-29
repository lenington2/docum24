<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipologia_id')->constrained()->onDelete('cascade');
            $table->foreignId('proyecto_id')->constrained()->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('archivo');
            $table->string('mime_type')->nullable();
            $table->string('descripcion')->nullable();
            $table->date('fecha_documento')->nullable();
            $table->longText('contenido_texto')->nullable();
            $table->longText('resumen_ia')->nullable();
            $table->string('categoria_ia')->nullable();
            $table->string('tipologia_ia')->nullable();
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'error'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
