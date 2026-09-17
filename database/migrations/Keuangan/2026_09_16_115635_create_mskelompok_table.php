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
        Schema::create('ms_kelompok', function (Blueprint $table) {
            $table->increments('mskId');
            $table->string('mskAkunKode', 20);
            $table->string('mskKode', 20)->unique();
            $table->string('mskNama', 100);
            $table->string('mskCreatedBy', 50)->nullable();
            $table->datetime('mskCreatedAt')->nullable();
            $table->string('mskUpdatedBy', 50)->nullable();
            $table->datetime('mskUpdatedAt')->nullable();
            $table->string('mskDeletedBy', 50)->nullable();
            $table->datetime('mskDeletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_kelompok');
    }
};
