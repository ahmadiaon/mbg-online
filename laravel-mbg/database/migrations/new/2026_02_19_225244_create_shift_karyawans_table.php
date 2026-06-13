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
        Schema::create('shift_karyawans', function (Blueprint $table) {
            $table->id();
            
            $table->string('uuid')->nullable();
            $table->string('nrp')->nullable();
            $table->date('date')->nullable();            
            $table->string('kode_shift')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_karyawans');
    }
};
