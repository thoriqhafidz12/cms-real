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
        Schema::create('kwitansi_counter', function (Blueprint $table) {
            $table->increments('kId');
            $table->string('kBulan', 2);
            $table->integer('kTahun');
            $table->integer('kCounter')->default(0);

            $table->unique(['kBulan', 'kTahun'], 'kwitansi_counter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansi_counter');
    }
};
