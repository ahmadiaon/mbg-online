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
        Schema::create('database_fields', function (Blueprint $table) {
            $table->id();    
            $table->string('code_table_field')->nullable();//CODE TABLE
            $table->string('description_field')->nullable();
            $table->string('type_data_field')->nullable();
            $table->string('level_data_field')->nullable();      
            $table->string('code_field')->nullable();      
            $table->string('visibility_data_field')->nullable();// VISIBILITY          
            $table->string('full_code_field')->nullable();
            $table->string('sort_field')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_fields');
    }
};
