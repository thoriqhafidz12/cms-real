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
        Schema::create('ms_jaminan', function (Blueprint $table) {
            $table->increments('msjId');
            $table->string('msjKode', 20)->unique();
            $table->string('msjNama', 225);
            $table->string('msjKeterangan', 225)->nullable();
            $table->string('msjCreatedBy', 50)->nullable();
            $table->datetime('msjCreatedAt')->nullable();
            $table->string('msjUpdatedBy', 50)->nullable();
            $table->datetime('msjUpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_jaminan');
    }
};
