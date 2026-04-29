<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('titulo')->default('Nuova conversazione');
            $table->json('historial'); // array de mensajes
            $table->timestamp('ultimo_mensaje_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'ultimo_mensaje_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversaciones');
    }
};
