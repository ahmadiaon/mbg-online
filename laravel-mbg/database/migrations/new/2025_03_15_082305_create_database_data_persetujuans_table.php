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
        Schema::create('database_data_persetujuans', function (Blueprint $table) {
            $table->id();            
            $table->string('code_form')->nullable();
            $table->string('code_data')->nullable();
            $table->string('level')->nullable();            
            $table->string('nrp')->nullable();         
            $table->string('status')->nullable();      
            $table->date('date_change')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_data_persetujuans');
    }
};
