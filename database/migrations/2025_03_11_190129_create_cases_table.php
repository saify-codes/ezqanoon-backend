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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->string('name');
            $table->string('type');
            $table->enum('urgency', ['URGENT', 'HIGH', 'MEDIUM', 'LOW'])->nullable();
            $table->string('court_name');
            $table->string('court_city')->nullable();
            $table->string('court_case_number');
            $table->string('judge_name')->nullable();
            $table->string('under_acts')->nullable();
            $table->string('under_sections')->nullable();
            $table->string('fir_number')->nullable();
            $table->string('fir_year')->nullable();
            $table->string('police_station')->nullable();
            $table->text('your_party_details')->nullable();
            $table->text('opposite_party_details')->nullable();
            $table->text('opposite_party_advocate_details')->nullable();
            $table->text('case_information')->nullable();
            $table->enum('status', ['OPEN', 'IN PROGRESS', 'CLOSED'])->default('OPEN');
            $table->enum('payment_status', ['PENDING', 'PAID', 'OVERDUE'])->default('PENDING');
            $table->timestamps();

            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');

            
        });

        Schema::create('case_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->string('file');
            $table->string('original_name');
            $table->string('mime_type');
            
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            
        });
        
        Schema::create('case_filling_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->string('description');
            $table->date('date');
            
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');

        });
        
        Schema::create('case_hearing_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->string('description');
            $table->date('date');
            
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
           
            
        });
        DB::statement("ALTER TABLE cases ADD CONSTRAINT cases_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
        DB::statement("ALTER TABLE case_filling_dates ADD CONSTRAINT case_filling_dates_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
        DB::statement("ALTER TABLE case_hearing_dates ADD CONSTRAINT case_hearing_dates_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_hearing_dates');
        Schema::dropIfExists('case_filling_dates');
        Schema::dropIfExists('case_attachments');
        Schema::dropIfExists('cases');
    }
};
