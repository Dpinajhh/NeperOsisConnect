<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topik', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 60)->unique();          // kata setelah '#'
            $table->string('slug', 80)->unique();
            $table->unsignedInteger('jumlah_post')->default(0);
            $table->timestamps();
        });

        // Pivot post <-> topik (hashtag)
        Schema::create('post_topik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('topik_id')->constrained('topik')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'topik_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_topik');
        Schema::dropIfExists('topik');
    }
};
