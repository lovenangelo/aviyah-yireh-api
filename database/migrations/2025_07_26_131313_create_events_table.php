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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->index();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->index();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('language_id')->nullable()->constrained();
            $table->string('title');
            $table->enum('event_mode', ['Online', 'In Person'])->default('In Person');
            $table->string('description');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('location');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('image_url')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('events');
    }
};
