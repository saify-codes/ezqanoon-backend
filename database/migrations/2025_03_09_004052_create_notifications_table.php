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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('title');
            $table->text('body')->nullable();
            $table->text('icon')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('id')->on('lawyers')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            
        });
        
        DB::statement(
            "ALTER TABLE notifications
             ADD CONSTRAINT ck_notifications_exactly_one_owner
             CHECK (
                 ((admin_id IS NOT NULL) +
                  (firm_id IS NOT NULL) +
                  (lawyer_id IS NOT NULL) +
                  (team_id IS NOT NULL)
                 ) = 1
             )"
        );        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
