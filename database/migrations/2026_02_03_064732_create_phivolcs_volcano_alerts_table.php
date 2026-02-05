<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phivolcs_volcano_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();

            $table->string('volcano')->nullable();          // 例: "Mayon Volcano"
            $table->string('alert_level')->nullable();      // 例: "Alert Level 2"
            $table->timestamp('issued_at')->nullable();     // 発表時刻
            $table->text('summary_text')->nullable();       // 要約
            $table->text('full_text')->nullable();          // 可能なら全文
            $table->string('source_url')->nullable();

            $table->timestamp('fetched_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phivolcs_volcano_alerts');
    }
};
