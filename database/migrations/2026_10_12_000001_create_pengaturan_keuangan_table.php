<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_keuangan', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->decimal('saldo_awal', 15, 2)->default(2000000);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_keuangan');
    }
};
