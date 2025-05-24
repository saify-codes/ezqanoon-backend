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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('origin', ['LOCAL', 'FOREIGN']);
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('type');
            $table->date('dob')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('communication_method')->nullable();
            $table->time('contact_time')->nullable();
            $table->string('language')->nullable();
            $table->text('billing_address')->nullable();
            $table->json('payment_methods')->nullable();
            $table->string('tin')->nullable();
            $table->json('tags')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');

            
        });
        
        Schema::create('client_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('file');
            $table->string('original_name');
            $table->string('mime_type');
            
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        DB::statement("ALTER TABLE clients ADD CONSTRAINT client_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_attachments');
        Schema::dropIfExists('clients');
    }
};
