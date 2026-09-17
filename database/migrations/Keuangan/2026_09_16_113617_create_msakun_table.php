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
        Schema::create('ms_akun', function (Blueprint $table) {
            $table->increments('msaId');
            $table->string('msaKode', 10)->unique();
            $table->string('msaNama', 225)->nullable();
            $table->string('msaCreatedBy', 50)->nullable();
            $table->datetime('msaCreatedAt')->nullable();
            $table->string('msaUpdatedBy', 50)->nullable();
            $table->datetime('msaUpdatedAt')->nullable();
            $table->string('msaDeletedBy', 50)->nullable();
            $table->datetime('msaDeletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_akun');
    }
};
