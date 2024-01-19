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
        Schema::create('absens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nama');
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->string('tanggal');
            $table->string('tanggal_keluar')->nullable();
            $table->time('jam_masuk', $precision = 0);
            $table->time('jam_keluar', $precision = 0)->nullable();
            $table->string('foto_masuk');
            $table->string('foto_keluar')->nullable();
            $table->string('lokasi_masuk');
            $table->string('lokasi_keluar')->nullable();
            $table->string('laporan_masuk');
            $table->string('laporan_keluar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
