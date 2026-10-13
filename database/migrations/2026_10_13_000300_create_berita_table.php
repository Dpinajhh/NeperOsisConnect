<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // penulis
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();

            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan');
            $table->longText('konten');
            $table->string('cover')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_headline')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('dilihat')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('is_headline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
