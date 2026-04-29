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
        Schema::create('suscripciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('plan_id')->constrained('planes')->onDelete('cascade');
    $table->bigInteger('tokens_usados')->default(0);
    $table->bigInteger('tokens_limite');
    $table->date('fecha_inicio');
    $table->date('fecha_fin');
    $table->enum('estado', ['activa', 'expirada', 'cancelada'])->default('activa');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
