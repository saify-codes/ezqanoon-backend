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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lawyer_id'); // Fixed typo from 'laywer_id' to 'lawyer_id'
            $table->unsignedBigInteger('user_id'); // Fixed typo from 'laywer_id' to 'lawyer_id'
            $table->text('feedback')->nullable();
            $table->tinyInteger('rating')->default(0);
            $table->timestamps();

            $table->foreign('lawyer_id')
                ->references('id')
                ->on('lawyers')
                ->onDelete('cascade');
                
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
        Schema::dropIfExists('ratings');
    }
};
