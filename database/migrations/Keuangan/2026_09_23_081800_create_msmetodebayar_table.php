<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ms_metodebayar', function (Blueprint $table) {
            $table->increments('mmId');
            $table->string('mmKode', 50)->nullable();
            $table->string('mmNama', 255)->nullable();
            $table->string('mmTipe', 50)->nullable();
            $table->string('mmBank', 100)->nullable();
            $table->string('mmNoRek', 100)->nullable();
            $table->string('mmAtasNama', 255)->nullable();
            $table->decimal('mmBiayaAdmin', 15, 2)->default(0);
            $table->string('mmAkunGl', 50)->nullable();
            $table->text('mmKeterangan')->nullable();
            $table->string('mmStatus', 20)->default('Active');
            
            $table->string('mmCreateBy', 50)->nullable();
            $table->datetime('mmCreateTime')->nullable();
            $table->string('mmUpdateBy', 50)->nullable();
            $table->datetime('mmUpdateTime')->nullable();
            $table->string('mmDeleteBy', 50)->nullable();
            $table->datetime('mmDeleteTime')->nullable();

            $table->index(['mmKode', 'mmNama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_metodebayar');
    }
};
