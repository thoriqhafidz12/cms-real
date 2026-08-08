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
        Schema::create('ms_jns_terima', function (Blueprint $table) {
            $table->bigInteger('msJnsId')->autoIncrement();
            $table->string('msJnsNama', 255)->nullable();
            $table->dateTime('msJnsCreateTime')->useCurrent();
            $table->string('msJnsCreatedBy', 255)->nullable();
            $table->dateTime('msJnsUpdateTime')->useCurrentOnUpdate()->nullable();
            $table->string('msJnsUpdatedBy', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jns_terima');
    }
};
