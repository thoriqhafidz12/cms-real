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
        Schema::create('tr_aset', function (Blueprint $table) {
            $table->bigIncrements('asId');
            $table->integer('asUserId');
            $table->string('asNamaBarang', 255)->nullable();
            $table->date('asTglTerima')->nullable();
            $table->integer('asTahun')->nullable();
            $table->decimal('asHarga', 20, 2);
            $table->integer('asMasaManfaat')->nullable();
            $table->string('asKeterangan', 255)->nullable();
            $table->string('asCreatedBy', 225)->nullable();
            $table->dateTime('asCreatedAt')->nullable();
            $table->string('asUpdatedBy', 225)->nullable();
            $table->dateTime('asUpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_aset');
    }
};
