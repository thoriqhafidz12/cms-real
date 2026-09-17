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
        Schema::create('ms_jns_simpanan', function (Blueprint $table) {
            $table->increments('mjsId');
            $table->string('mjsKode', 10)->unique();
            $table->string('mjsNama', 100);
            $table->text('mjsKeterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jns_simpanan');
    }
};
