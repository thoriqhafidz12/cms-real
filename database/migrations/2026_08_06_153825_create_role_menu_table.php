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
        Schema::create('role_menu', function (Blueprint $table) {
            $table->bigIncrements('rmId');
            $table->unsignedBigInteger('rmRoleId');
            $table->unsignedBigInteger('rmMenuId');
            $table->dateTime('rmCreatedAt')->useCurrent();
            $table->dateTime('rmUpdatedAt')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menu');
    }
};
