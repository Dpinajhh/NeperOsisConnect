<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Program kerja OSIS
        Schema::create('program_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->string('bidang', 100)->nullable();

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            // rencana = akan datang, berjalan = sedang berjalan, selesai, batal
            $table->enum('status', ['rencana', 'berjalan', 'selesai', 'batal'])->default('rencana');
            $table->unsignedTinyInteger('progres')->default(0);   // 0-100

            $table->string('penanggung_jawab')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'tanggal_mulai']);
        });

        // Kegiatan / event sekolah
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->string('poster')->nullable();
            $table->string('lokasi')->nullable();

            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();

            $table->unsignedInteger('kuota')->nullable();   // null = tanpa batas
            $table->enum('status', ['draft', 'terbit'])->default('terbit');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'waktu_mulai']);
        });

        // Pendaftaran peserta event
        Schema::create('event_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_peserta');
        Schema::dropIfExists('event');
        Schema::dropIfExists('program_kerja');
    }
};
