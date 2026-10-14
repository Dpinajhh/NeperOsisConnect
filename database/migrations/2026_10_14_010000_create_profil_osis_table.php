<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Profil OSIS — satu baris data (singleton)
        Schema::create('profil_osis', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 20)->default('2026/2027');
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('sejarah')->nullable();
            $table->text('sambutuan')->nullable();
            $table->string('sambutuan_pengirim')->nullable();

            // Kontak
            $table->string('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('instagram', 60)->nullable();

            $table->timestamps();
        });

        // Kepengurusan OSIS
        Schema::create('kepengurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->string('bidang', 100)->nullable();
            $table->string('foto')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index('urutan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepengurusan');
        Schema::dropIfExists('profil_osis');
    }
};
