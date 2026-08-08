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
        Schema::create('ms_jns_keluar', function (Blueprint $table) {
            $table->bigInteger('msJnsKelId')->autoIncrement();
            $table->string('msJnsKelNama', 255)->nullable();
            $table->dateTime('msJnsKelCreateTime')->useCurrent();
            $table->string('msJnsKelCreatedBy', 255)->nullable();
            $table->dateTime('msJnsKelUpdateTime')->useCurrentOnUpdate()->nullable();
            $table->string('msJnsKelUpdatedBy', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jns_keluar');
    }
};
