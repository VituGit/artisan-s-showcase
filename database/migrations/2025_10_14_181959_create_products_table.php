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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atelier_id');

            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('name', 150);
            $table->string('slug', 160);
            $table->string('short_desc', 180)->nullable();
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->unsigned();

            $table->boolean('is_personalizable')->default(false);
            $table->string('personalization_hint', 140)->nullable();

            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('position')->default(0);
            $table->dateTime('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['atelier_id', 'slug']);
            $table->index('atelier_id');
            $table->index('category_id');
            $table->index('is_available');
            $table->index('is_featured');
            $table->index('published_at');
            $table->index('position');

            $table->foreign('atelier_id')
                ->references('id')
                ->on('ateliers')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
