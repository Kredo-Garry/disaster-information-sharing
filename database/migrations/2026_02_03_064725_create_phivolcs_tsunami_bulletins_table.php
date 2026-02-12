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

            $table->string('bulletin_no')->nullable();
            $table->string('status')->nullable();

            $table->timestamp('issued_at')->nullable();

            $table->text('summary_text')->nullable();
            $table->longText('full_text')->nullable();

            $table->string('source_url')->nullable();
            $table->timestamp('fetched_at')->nullable();

            $table->timestamps();

            // 🔥 追加（検索高速化）
            $table->index('issued_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phivolcs_tsunami_bulletins');
    }
};
