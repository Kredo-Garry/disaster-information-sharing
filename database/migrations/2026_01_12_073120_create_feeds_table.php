<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();

            // 投稿者（ユーザー投稿用）
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // 外部ソース系
            $table->string('source_platform');
            $table->string('external_author')->nullable();
            $table->string('original_url')->nullable();

            // 本文
            $table->text('content');

            // タグ
            $table->json('tags')->nullable();

            // 公開日時
            $table->timestamp('published_at')->nullable();

            // 表示制御
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_weight')->default(0);

            // 埋め込み用HTML
            $table->longText('embed_html')->nullable();

            $table->timestamps();

            // 🔥 パフォーマンス向上
            $table->index('published_at');
            $table->index('is_visible');
            $table->index('source_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
