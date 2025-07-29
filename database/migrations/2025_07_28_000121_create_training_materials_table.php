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
        Schema::create("categories", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name")->unique()->index();
        });

        Schema::create("languages", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name")->unique()->index();
        });

        Schema::create('training_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId("category_id")->constrained();
            $table->foreignId("language_id")->constrained();

            $table->timestamps();
            $table->date("expiration_date");

            $table->string("title");
            $table->text("description");
            $table->integer("duration")->nullable();
            $table->string("path");
            $table->string("thumbnail_path");
            $table->unsignedInteger("views")->default(0);
            $table->boolean("is_visible")->default(true);

            $table->index('views');
            $table->index('category_id');
            $table->index('created_at');
            $table->index('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_materials');
    }
};
