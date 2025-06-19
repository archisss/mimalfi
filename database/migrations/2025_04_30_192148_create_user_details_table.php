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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('client_reference')->nullable();
            $table->string('work_address')->nullable();
            $table->string('payment_address')->nullable();
            $table->string('picture')->nullable(); //eliminar antes de subir a prod
            $table->string('picture_ine')->nullable();
            $table->string('picture_domicilio')->nullable();
            $table->string('picture_foto')->nullable();
            $table->string('aval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
