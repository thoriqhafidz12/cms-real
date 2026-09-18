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
        Schema::create('ms_jnspinjaman', function (Blueprint $table) {
            $table->increments('mjPinjamanId');
            $table->string('mjPinjamanKode', 100)->nullable();
            $table->string('mjPinjamanNama', 255)->nullable();
            $table->decimal('mjSukuBunga', 5, 2)->default(0);
            $table->string('mjTipeBunga', 20)->default('Flat')->comment('Flat atau Efektif');
            $table->decimal('mjPlafonMaksimal', 15, 2)->default(0);
            $table->integer('mjTenorMaksimal')->default(0);
            $table->decimal('mjBiayaAdmin', 5, 2)->default(0)->comment('Persentase biaya admin');
            $table->decimal('mjBiayaProvisi', 5, 2)->default(0)->comment('Persentase biaya provisi');
            $table->decimal('mjDendaKeterlambatan', 5, 2)->default(0)->comment('Persentase denda per bulan');
            $table->string('mjAkunPiutang', 50)->nullable()->comment('Akun GL Piutang Pinjaman');
            $table->string('mjAkunBunga', 50)->nullable()->comment('Akun GL Pendapatan Bunga');
            $table->string('mjAkunAdmin', 50)->nullable()->comment('Akun GL Pendapatan Admin/Provisi');
            $table->string('mjAkunDenda', 50)->nullable()->comment('Akun GL Pendapatan Denda');
            $table->text('mjKeterangan')->nullable();
            $table->string('mjStatus', 20)->default('Active');
            $table->string('mjCreateUser', 50)->nullable();
            $table->datetime('mjCreateTime')->nullable();
            $table->string('mjUpdateUser', 50)->nullable();
            $table->datetime('mjUpdateTime')->nullable();
            $table->string('mjDeleteUser', 50)->nullable();
            $table->datetime('mjDeleteTime')->nullable();

            $table->index(['mjPinjamanKode', 'mjPinjamanNama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jnspinjaman');
    }
};
