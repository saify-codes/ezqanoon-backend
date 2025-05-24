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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->unsignedBigInteger('assign_to')->nullable();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['PENDING', 'IN PROGRESS', 'COMPLETED']);
            $table->timestamps();

              
            $table->foreign('assign_to')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');

            
        });

        DB::statement("ALTER TABLE tasks ADD CONSTRAINT task_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
