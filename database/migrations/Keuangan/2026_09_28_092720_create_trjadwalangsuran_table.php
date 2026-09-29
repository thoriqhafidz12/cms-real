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
        Schema::create('tr_jadwalangsuran', function (Blueprint $table) {
            $table->increments('tjaId');
            $table->integer('tjaPinjamanId')->unsigned();
            $table->foreign('tjaPinjamanId')->references('trpjId')->on('tr_pinjaman')->onDelete('cascade');
            $table->date('tjaTanggalAngsuran');
            $table->integer('tjaCicilanKe');
            $table->decimal('tjaNominalAngsuran', 20, 2);
            $table->decimal('tjaNominalBunga', 20, 2);
            $table->decimal('tjaTotalTagihan', 20, 2)->default(0);
            $table->decimal('tjaSisaTagihan', 20, 2)->default(0);
            $table->string('tjaStatus', 30)->default('0')->comment('0 : Belum Bayar, 1 : Sudah Bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jadwalangsuran');
    }
};
