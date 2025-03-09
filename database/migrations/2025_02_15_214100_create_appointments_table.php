<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lawyer_id');
            $table->unsignedBigInteger('user_id');
            $table->text('details');
            $table->string('meeting_link')->nullable();
            $table->timestamp('meeting_date');
            $table->timestamps();

            // Foreign keys with indexes
            $table->foreign('lawyer_id')
                ->references('id')
                ->on('lawyers')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Additional indexes for frequent queries
            $table->index('meeting_date');
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->timestamps();

            // Foreign key with index
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->onDelete('cascade');

            // Index for file metadata if needed
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('appointments');
    }
};