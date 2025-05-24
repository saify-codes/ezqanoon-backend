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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firm_id')->index()->nullable();
            $table->unsignedBigInteger('lawyer_id')->index()->nullable();
            $table->enum('day', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'])->index();
            $table->time('time')->index();

            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
        });

        DB::statement("ALTER TABLE availabilities ADD CONSTRAINT availabilities_exactly_one_owner CHECK ((lawyer_id IS NOT NULL AND firm_id IS NULL) OR (lawyer_id IS NULL AND firm_id IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
