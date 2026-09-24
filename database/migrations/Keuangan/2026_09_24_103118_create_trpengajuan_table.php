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
        Schema::create('tr_pengajuan', function (Blueprint $table) {
            $table->id('tpId');
            $table->string('tpKode', 225)->nullable();
            $table->integer('tpAnggotaId');
            $table->string('tpAnggotaNama', 225);
            $table->date('tpTanggalPinjam')->nullable();
            $table->string('tpJaminanId',225);
            $table->string('tpJaminanNama', 225)->nullable();
            $table->string('tpTujuanId',225);
            $table->string('tpTujuanNama', 225)->nullable();
            $table->decimal('tpJumlahPinjam', 20, 2)->default(0);
            $table->decimal('tpJumlahAngsuran', 20, 2)->default(0);
            $table->integer('tpJumlahAngsuranBulan')->default(0);
            $table->decimal('tpBunga', 5, 2)->default(0);
            $table->decimal('tpTotalPinjam', 20, 2)->default(0);
            $table->string('tpStatus', 20)->default('Active');
            $table->text('tpKeterangan')->nullable();

            $table->string('tpCreatedBy', 50)->nullable();
            $table->datetime('tpCreatedAt')->nullable();
            $table->string('tpUpdatedBy', 50)->nullable();
            $table->datetime('tpUpdatedAt')->nullable();
            $table->string('tpDeletedBy', 50)->nullable();
            $table->datetime('tpDeletedAt')->nullable();

            $table->index(['tpKode', 'tpAnggotaId', 'tpJaminanId', 'tpStatus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pengajuan');
    }
};
