<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // ✅ color_code カラムを追加！ icon カラムの後ろに置くにょ
            $table->string('color_code')->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // ✅ もしロールバック（戻す）した時はこのカラムを消すにょ
            $table->dropColumn('color_code');
        });
    }
};