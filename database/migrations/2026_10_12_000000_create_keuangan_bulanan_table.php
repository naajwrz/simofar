<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_bulanan', function (Blueprint $table) {
            $table->id('id_keuangan_bulanan');
            $table->date('periode')->unique();
            $table->decimal('gaji_per_apoteker', 15, 2)->default(0);
            $table->unsignedSmallInteger('jumlah_apoteker')->default(0);
            $table->decimal('pajak_persen', 5, 2)->default(0);
            $table->decimal('biaya_operasional', 15, 2)->default(0);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_bulanan');
    }
};
