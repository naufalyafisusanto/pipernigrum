<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedInteger('id_session');
            $table->foreign('id_session')->references('id')->on('sessions')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('timestamp');
            $table->unsignedSmallInteger('voltage');           // V
            $table->unsignedFloat('current', 8, 2);            // A
            $table->unsignedFloat('power', 16, 2);             // Watt
            $table->unsignedSmallInteger('frequency');         // Hz
            $table->unsignedFloat('power_factor', 4, 2);
            $table->unsignedFloat('temp', 6, 2);               // °C
            $table->unsignedSmallInteger('humidity');          // %
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
