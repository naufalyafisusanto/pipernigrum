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
        Schema::create('stations', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('token', 10);
            $table->string('name', 20)->unique();
            $table->ipAddress('ip_address')->unique();
            $table->macAddress('mac_address');
            $table->timestamp('added_at');
            $table->boolean('active')->default(true);
            $table->boolean('running')->default(false);
            $table->tinyInteger('rotation')->default(0);
            $table->unsignedFloat('mass', 15, 2)->default(0);         // Kg
            $table->unsignedBigInteger('duration')->default(0);       // s
            $table->unsignedFloat('energy', 10, 2)->default(0);       // kWh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
