<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polling', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();

            // draft = belum tampil, terbit = berlangsung, ditutup = selesai
            $table->enum('status', ['draft', 'terbit', 'ditutup'])->default('terbit');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('polling_pilihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('polling')->cascadeOnDelete();
            $table->string('teks', 200);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('polling_suara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('polling')->cascadeOnDelete();
            $table->foreignId('pilihan_id')->constrained('polling_pilihan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Satu pengguna hanya boleh memilih satu kali per polling
            $table->unique(['polling_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polling_suara');
        Schema::dropIfExists('polling_pilihan');
        Schema::dropIfExists('polling');
    }
};
