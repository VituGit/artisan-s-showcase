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
        Schema::create('atelier_lp_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atelier_id')->unique();

            $table->smallInteger('schema_version')->default(1);

            $table->json('data')->nullable();
            $table->json('draft')->nullable();

            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamp('published_at')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('updated_by');


            $table->foreign('atelier_id')
                ->references('id')
                ->on('ateliers')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atelier_lp_configs');
    }
};
