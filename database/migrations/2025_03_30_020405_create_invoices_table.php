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
            $table->string('issuer_name');
            $table->string('issuer_email');
            $table->string('issuer_phone');
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->string('recipient_phone');
            $table->string('recipient_address')->nullable();
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->json('summary');
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
