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
        Schema::create('mapping_pengeluaran', function (Blueprint $table) {
            $table->increments('mapId');
            $table->string('mapKodeAsal', 20);
            $table->string('mapNamaAsal', 100);
            $table->string('mapKodeDebet', 20);
            $table->string('mapNamaDebet', 100);
            $table->string('mapKodeKredit', 20);
            $table->string('mapNamaKredit', 100);
            $table->integer('mapStatusPenagihan')->default(0)->comment('0. Tidak menggunakan hutang, 1. Menggunakan Hutang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_pengeluaran');
    }
};
