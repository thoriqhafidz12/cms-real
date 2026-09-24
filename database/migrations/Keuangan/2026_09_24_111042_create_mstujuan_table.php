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
        Schema::create('ms_tujuanpinjaman', function (Blueprint $table) {
            $table->increments('mstId');
            $table->string('mstKode', 20)->unique();
            $table->string('mstNama', 225);
            $table->string('mstKeterangan', 225)->nullable();
            $table->string('mstCreatedBy', 50)->nullable();
            $table->datetime('mstCreatedAt')->nullable();
            $table->string('mstUpdatedBy', 50)->nullable();
            $table->datetime('mstUpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_tujuanpinjaman');
    }
};
