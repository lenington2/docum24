<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitsToplanesTable extends Migration
{
    public function up()
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->integer('max_proyectos')->default(1)->after('tokens_mes');
            $table->integer('max_categorias')->default(5)->after('max_proyectos');
            $table->integer('max_tipologias')->default(5)->after('max_categorias');
            $table->bigInteger('max_storage_mb')->default(100)->after('max_tipologias');
        });
    }

    public function down()
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->dropColumn(['max_proyectos', 'max_categorias', 'max_tipologias', 'max_storage_mb']);
        });
    }
}
