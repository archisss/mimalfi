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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // cliente
            $table->string('alias');
            $table->bigInteger('amount');
            //$table->string('loan_type');
            $table->string('payment_date'); // Ej: Lunes, Martes
            $table->string('payment_type'); // Ej. Efectivo, Digital
            $table->time('payment_time')->default('08:00');
            $table->date('term');
            $table->string('status')->default('activo');
            $table->boolean('use_bank')->default(false);
            $table->unsignedBigInteger('use_lender')->nullable();
            $table->unsignedBigInteger('collector')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
