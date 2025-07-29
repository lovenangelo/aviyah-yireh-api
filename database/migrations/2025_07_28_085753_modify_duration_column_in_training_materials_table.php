<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDurationColumnInTrainingMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->float('duration')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('training_materials', function (Blueprint $table) {
            $table->integer('duration')->nullable()->change();
        });
    }
}
