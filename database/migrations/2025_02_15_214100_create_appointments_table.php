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
            $table->string('country')->default('Pakistan');
            $table->text('details');
            $table->text('summary')->nullable();
            $table->text('meeting_link_user')->nullable();
            $table->text('meeting_link_lawyer')->nullable();
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

        Schema::create('appointment_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('file');
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

        Schema::create('appointment_summary_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('file');
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
        Schema::dropIfExists('appointment_summary_attachments');
        Schema::dropIfExists('appointment_attachments');
        Schema::dropIfExists('appointments');
    }
};