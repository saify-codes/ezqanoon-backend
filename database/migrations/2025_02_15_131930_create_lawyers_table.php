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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('phone');
            $table->string('password');
            $table->string('location')->nullable();
            $table->time('availability_from')->nullable();
            $table->time('availability_to')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience')->nullable();
            $table->float('price')->nullable();
            $table->string('qualification')->nullable();
            $table->text('description')->nullable();
            $table->boolean('verified_email')->default(false);
            $table->string('verification_token')->nullable();
            $table->dateTime('verification_token_expiry')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyers');
    }
};
