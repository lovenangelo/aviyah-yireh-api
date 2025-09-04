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
        Schema::create('item_set_ups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('cost', 8, 2);
            $table->decimal('selling_price', 8, 2);
            $table->string('description', length: 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_set_ups');
    }
};
