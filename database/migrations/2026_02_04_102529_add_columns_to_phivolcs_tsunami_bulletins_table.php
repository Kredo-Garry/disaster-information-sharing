<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('phivolcs_tsunami_bulletins', function (Blueprint $table) {
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'hash')) {
                $table->string('hash')->unique()->after('id');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'bulletin_no')) {
                $table->string('bulletin_no')->nullable()->after('hash');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'status')) {
                $table->string('status')->nullable()->after('bulletin_no');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'summary_text')) {
                $table->text('summary_text')->nullable()->after('issued_at');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'full_text')) {
                $table->longText('full_text')->nullable()->after('summary_text');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'source_url')) {
                $table->string('source_url')->nullable()->after('full_text');
            }
            if (!Schema::hasColumn('phivolcs_tsunami_bulletins', 'fetched_at')) {
                $table->timestamp('fetched_at')->nullable()->after('source_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('phivolcs_tsunami_bulletins', function (Blueprint $table) {
            // down は事故防止のため何もしない（必要なら手動で）
        });
    }
};
