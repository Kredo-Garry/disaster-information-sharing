<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            // user_id（ユーザー投稿用。なければ null）
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();

            // タグ（json）
            $table->json('tags')->nullable()->after('original_url');

            // 投稿日時
            $table->timestamp('published_at')->nullable()->after('tags');

            // 表示制御・並び
            $table->boolean('is_visible')->default(true)->after('published_at');
            $table->integer('sort_weight')->default(0)->after('is_visible');

            // 埋め込みHTML
            $table->longText('embed_html')->nullable()->after('sort_weight');
        });
    }

    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            // 外部キーがあるので先に落とす
            $table->dropConstrainedForeignId('user_id');

            $table->dropColumn([
                'tags',
                'published_at',
                'is_visible',
                'sort_weight',
                'embed_html',
            ]);
        });
    }
};
