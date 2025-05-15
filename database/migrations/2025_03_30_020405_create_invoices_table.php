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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('case_type')->nullable();
            $table->enum('type', ['ONE TIME', 'MILESTONE'])->default('ONE TIME');
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE'])->nullable();
            $table->date('due_date')->nullable();
            $table->enum('payment_method', ['CASH', 'BANK', 'ONLINE TRANSFER']);
            $table->json('receipt')->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoice_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->date('due_date');
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE'])->default('PENDING');

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_milestones');
        Schema::dropIfExists('invoices');
    }
};
