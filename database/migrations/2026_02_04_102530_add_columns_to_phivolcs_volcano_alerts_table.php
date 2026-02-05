<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('phivolcs_volcano_alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'hash')) {
                $table->string('hash')->unique()->after('id');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'volcano')) {
                $table->string('volcano')->nullable()->after('hash');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'alert_level')) {
                $table->string('alert_level')->nullable()->after('volcano');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('alert_level');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'summary_text')) {
                $table->text('summary_text')->nullable()->after('issued_at');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'full_text')) {
                $table->longText('full_text')->nullable()->after('summary_text');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'source_url')) {
                $table->string('source_url')->nullable()->after('full_text');
            }
            if (!Schema::hasColumn('phivolcs_volcano_alerts', 'fetched_at')) {
                $table->timestamp('fetched_at')->nullable()->after('source_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('phivolcs_volcano_alerts', function (Blueprint $table) {
            // down は事故防止のため何もしない
        });
    }
};
