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
        Schema::create('product_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');

            $table->string('file_path', 255); // Original image path
            $table->string('thumb_path', 255)->nullable(); // Thumbnail (400x400)
            $table->string('medium_path', 255)->nullable(); // Medium (800x800)
            $table->string('url', 255)->nullable(); // Legacy/external URL
            $table->string('alt_text', 160)->nullable();
            $table->boolean('is_cover')->default(false);
            $table->integer('position')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id');
            $table->index('is_cover');
            $table->index('position');
            $table->index(['product_id', 'position']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_photos');
    }
};
