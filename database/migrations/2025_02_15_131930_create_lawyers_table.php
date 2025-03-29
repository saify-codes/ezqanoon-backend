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
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_profile_completed')->default(0);
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->date('subscription_expires_at')->nullable();
            $table->enum('role', ['ADMIN', 'USER'])->default('ADMIN');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');

        });
        
        // Create additional tables (tokens)
        Schema::create('lawyer_verification_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('lawyer_id');
            $table->string('token');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('lawyer_id')
                ->references('id')
                ->on('lawyers')
                ->onDelete('cascade');
        });

        // Create additional tables (tokens)
        Schema::create('lawyer_password_reset_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('lawyer_id');
            $table->string('token');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('lawyer_id')
                ->references('id')
                ->on('lawyers')
                ->onDelete('cascade');
        });

        // Check constraint to ensure:
        // - Admins must have lawyer_id as NULL.
        // - Users must have lawyer_id NOT NULL.
        DB::statement("ALTER TABLE lawyers ADD CONSTRAINT check_role_lawyer CHECK ((role = 'ADMIN' AND lawyer_id IS NULL) OR (role = 'USER' AND lawyer_id IS NOT NULL))");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_password_reset_tokens');
        Schema::dropIfExists('lawyer_verification_tokens');
        Schema::dropIfExists('lawyers');
    }
};
