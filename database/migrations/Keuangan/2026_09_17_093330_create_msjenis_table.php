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
        Schema::create('ms_jenis', function (Blueprint $table) {
            $table->increments('msjId');
            $table->string('msjAkunKode', 20);
            $table->string('msjKelompokKode', 20);
            $table->string('msjKode', 20)->unique();
            $table->string('msjNama', 100);
            $table->datetime('msjCreatedAt')->nullable();
            $table->string('msjCreatedBy', 50)->nullable();
            $table->datetime('msjUpdatedAt')->nullable();
            $table->string('msjUpdatedBy', 50)->nullable();
            $table->datetime('msjDeletedAt')->nullable();
            $table->string('msjDeletedBy', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jenis');
    }
};
