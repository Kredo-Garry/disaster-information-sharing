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

            // ✅ 統一：ここだけを使う
            $table->string('volcano_name')->nullable();     // 例: "Mayon Volcano"

            $table->string('alert_level')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->text('summary_text')->nullable();
            $table->longText('full_text')->nullable();      // 長くなるので longText 推奨
            $table->string('source_url')->nullable();
            $table->timestamp('fetched_at')->nullable();

            $table->timestamps();

            // 任意だけど便利
            $table->index('volcano_name');
            $table->index('issued_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phivolcs_volcano_alerts');
    }
};
