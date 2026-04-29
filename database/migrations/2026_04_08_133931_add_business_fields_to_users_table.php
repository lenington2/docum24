<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('business_type')->nullable()->after('email');
        $table->longText('ai_prompt')->nullable()->after('business_type');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['business_type', 'ai_prompt']);
    });
}
};
