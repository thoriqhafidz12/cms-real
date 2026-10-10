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
        Schema::create('tr_keluar', function (Blueprint $table) {
            $table->increments('kId');
            $table->string('kSumber', 50)->nullable();
            $table->integer('kSumberId')->nullable();
            $table->string('kNo', 100)->nullable();
            $table->decimal('kNilai', 20, 2)->nullable();
            $table->date('kTgl')->nullable();
            $table->string('kKeterangan')->nullable();
            $table->string('kCoa', 20)->nullable();
            $table->string('kPenagihan', 100)->nullable();
            $table->integer('kStatus')->nullable();
            $table->decimal('kTerpakai', 20, 2)->nullable();
            $table->integer('kIdPengajuanBelanja')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_keluar');
    }
};
