<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('konten');
            $table->string('gambar')->nullable();

            // 'sekolah' = semua siswa, 'kelas' = satu kelas, 'jurusan' = satu jurusan
            $table->enum('visibilitas', ['sekolah', 'kelas', 'jurusan'])->default('sekolah');

            $table->unsignedInteger('jumlah_suka')->default(0);
            $table->unsignedInteger('jumlah_komentar')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['visibilitas', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
