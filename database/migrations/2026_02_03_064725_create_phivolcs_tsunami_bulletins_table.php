<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phivolcs_tsunami_bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();

            $table->string('bulletin_no')->nullable();       // 例: "Tsunami Advisory No. 03"
            $table->string('status')->nullable();           // 例: "Tsunami Information"
            $table->timestamp('issued_at')->nullable();     // 発表時刻
            $table->text('summary_text')->nullable();       // 本文要約（strip_tags後の短いテキスト）
            $table->text('full_text')->nullable();          // 可能なら全文（長い場合）
            $table->string('source_url')->nullable();

            $table->timestamp('fetched_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phivolcs_tsunami_bulletins');
    }
};
