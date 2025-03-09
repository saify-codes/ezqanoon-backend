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
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('lawyer_verification_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('lawyer_id');
            $table->string('token');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('lawyer_id')
                ->references('id')
                ->on('lawyers')
                ->onDelete('cascade');
            });
            
            Schema::create('lawyer_password_reset_tokens', function (Blueprint $table) {
                $table->unsignedBigInteger('lawyer_id');
                $table->string('token');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

                $table->foreign('lawyer_id')
                    ->references('id')
                    ->on('lawyers')
                    ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyers');
        Schema::dropIfExists('lawyer_verification_tokens');
        Schema::dropIfExists('lawyer_password_reset_tokens');
    }
};
