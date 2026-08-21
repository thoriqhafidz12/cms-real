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
        Schema::create('ms_anggota', function (Blueprint $table) {
            $table->increments('maId');
            $table->string('maKode', 50)->nullable();
            $table->string('maNama', 255)->nullable();
            $table->text('maAlamat')->nullable();
            $table->string('maNoTelp', 20)->nullable();
            $table->string('maNoIdentitas', 50)->nullable();
            $table->string('maTempatLahir', 100)->nullable();
            $table->date('maTglLahir')->nullable();
            $table->string('maJnsKelamin', 20)->nullable();
            $table->string('maPekerjaan', 100)->nullable();
            $table->date('maTglGabung')->nullable();
            $table->string('maStatusPernikahan', 50)->nullable();
            $table->string('maNamaIbuKandung', 100)->nullable();
            $table->string('maStatus', 20)->default('Active');
            $table->string('maCreatedBy', 50)->nullable();
            $table->datetime('maCreatedAt')->nullable();
            $table->string('maUpdatedBy', 50)->nullable();
            $table->datetime('maUpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_anggota');
    }
};
