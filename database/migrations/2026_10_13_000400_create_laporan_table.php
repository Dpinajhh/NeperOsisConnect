<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelapor_id')->constrained('users')->cascadeOnDelete();

            // Polimorfik: bisa melaporkan Post maupun Komentar
            $table->morphs('laporanable');

            $table->text('alasan');
            // status: 'open' (baru), 'diproses', 'selesai', 'ditolak'
            $table->enum('status', ['open', 'diproses', 'selesai', 'ditolak'])->default('open')->index();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
