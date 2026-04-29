<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('suscripciones', function (Blueprint $table) {
        $table->string('stripe_subscription_id')->nullable()->after('estado');
        $table->string('stripe_price_id')->nullable()->after('stripe_subscription_id');
    });
}

public function down()
{
    Schema::table('suscripciones', function (Blueprint $table) {
        $table->dropColumn(['stripe_subscription_id', 'stripe_price_id']);
    });
}
};
