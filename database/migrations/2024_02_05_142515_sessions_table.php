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
        Schema::create('sessions', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('id_station');
            $table->foreign('id_station')->references('id')->on('stations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('token', 10);
            $table->timestamp('start_at');
            $table->unsignedSmallInteger('initial_mass');               // g
            $table->timestamp('eta')->nullable();    
            $table->timestamp('end_at')->nullable();    
            $table->unsignedSmallInteger('final_mass')->default(0);     // g
            $table->unsignedMediumInteger('duration')->default(0);
            $table->unsignedFloat('energy', 10, 2)->default(0);         // Wh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
