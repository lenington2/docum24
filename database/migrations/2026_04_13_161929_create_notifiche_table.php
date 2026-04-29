<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifiche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->date('fecha_scadenza');
            $table->integer('dias_antes')->default(7);
            $table->string('email');
            $table->enum('estado', ['pendiente', 'enviada', 'cancelada'])->default('pendiente');
            $table->timestamp('enviada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifiche');
    }
};
