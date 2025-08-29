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
        Schema::create('training_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('language_id')->constrained();

            $table->timestamps();
            $table->date('expiration_date')->nullable();

            $table->string('title');
            $table->text('description');
            $table->integer('duration')->nullable();
            $table->json('files');
            $table->string('thumbnail_path');
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('status')->default(0);

            $table->index('views');
            $table->index('category_id');
            $table->index('created_at');
            $table->index('language_id');
            $table->index('is_visible');
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
