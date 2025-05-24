<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the lawyers table with the check constraint
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER']);
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('phone');
            $table->string('password');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->json('specialization')->nullable();
            $table->integer('experience')->nullable();
            $table->float('price')->nullable();
            $table->string('qualification')->nullable();
            $table->text('description')->nullable();
            $table->text('licence_number')->nullable();
            $table->text('cnic')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->decimal('earnings')->default(0);
            $table->boolean('is_profile_completed')->default(0);
            $table->boolean('is_blocked')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_subscription_expired')->default(0);
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');

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
