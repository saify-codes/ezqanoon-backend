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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->boolean('global')->nullable();
            $table->unsignedBigInteger('admin_id')->index()->nullable();
            $table->unsignedBigInteger('lawyer_id')->index()->nullable();
            $table->unsignedBigInteger('firm_id')->index()->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->string('name');
            $table->text('value');

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->unique(['global',    'name']);
            $table->unique(['admin_id',  'name']);
            $table->unique(['lawyer_id', 'name']);
            $table->unique(['firm_id',   'name']);
            $table->unique(['team_id',   'name']);
        });

        // ✅ global can only be NULL or 1 (never 0)
        DB::statement("
            ALTER TABLE options
            ADD CONSTRAINT ck_options_global_value
            CHECK (global = 1 OR global IS NULL)
        ");


        DB::statement("
             ALTER TABLE options
             ADD CONSTRAINT ck_options_exactly_one_owner
             CHECK (
                 (
                  (global IS NOT NULL) +
                  (admin_id IS NOT NULL) +
                  (firm_id IS NOT NULL) +
                  (lawyer_id IS NOT NULL) +
                  (team_id IS NOT NULL)
                 ) = 1
            )
        ");
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
