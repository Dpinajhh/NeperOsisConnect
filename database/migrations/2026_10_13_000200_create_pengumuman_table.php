<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // penulis
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();

            $table->string('judul');
            $table->string('slug')->unique();
            $table->longText('konten');
            $table->string('cover')->nullable();

            // 'semua' = seluruh sekolah, 'kelas' = satu kelas, 'jurusan' = satu jurusan
            $table->enum('target', ['semua', 'kelas', 'jurusan'])->default('semua');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->cascadeOnDelete();

            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('dilihat')->default(0);

            $table->timestamps();

            $table->index(['published_at', 'is_pinned']);
            $table->index('target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
