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
        Schema::create('tr_pinjaman', function (Blueprint $table) {
            $table->increments('trpjId');
            $table->integer('trpPengajuanId');
            $table->integer('trpAnggotaId');
            $table->string('trpAnggotaNama', 225);
            $table->integer('trpMetodBayarId');
            $table->string('trpMetodBayarNama', 225);
            $table->string('trpNoPinjaman', 50)->nullable();
            $table->date('trpTanggalCair')->nullable();
            $table->decimal('trpNominalPinjaman', 20, 2)->default(0);
            $table->integer('trpTenor')->default(1);
            $table->decimal('trpBunga', 5, 2)->default(0);
            $table->decimal('trpBiayaAdmin', 20, 2)->default(0);
            $table->decimal('trpCicilanPokok', 20, 2)->default  (0);
            $table->decimal('trpCicilanBunga', 20, 2)->default(0);
            $table->decimal('trpTotalCicilan', 20, 2)->default(0);
            $table->text('trpKeterangan')->nullable();
            $table->integer('trpStatusPinjaman')->default(0)->comment('0: Belum Lunas, 1: Lunas');

            $table->string('trpCreatedUser', 50)->nullable();
            $table->datetime('trpCreatedAt')->nullable();
            $table->string('trpUpdatedUser', 50)->nullable();
            $table->datetime('trpUpdatedAt')->nullable();

            $table->index(['trpPengajuanId', 'trpAnggotaId', 'trpMetodBayarId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pinjaman');
    }
};
