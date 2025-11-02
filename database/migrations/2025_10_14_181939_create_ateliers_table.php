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
        Schema::create('ateliers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->string('tagline', 160)->nullable();
            $table->text('bio')->nullable();

            $table->string('phone_whatsapp', 20)->nullable();
            $table->string('instagram_handle', 60)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->string('cover_url', 255)->nullable();

            $table->string('city', 80)->nullable();
            $table->char('state', 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('is_active');
            $table->index('created_at');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ateliers');
    }
};
