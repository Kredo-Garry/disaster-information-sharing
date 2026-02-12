<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Family
            $table->string('family_id', 50)->nullable()->index()->after('is_admin');

            // MyPage status
            $table->enum('status', ['neutral', 'safe', 'help'])->default('neutral')->after('family_id');
            $table->text('status_message')->nullable()->after('status');
            $table->timestamp('status_updated_at')->nullable()->after('status_message');
            $table->timestamp('status_expires_at')->nullable()->after('status_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['family_id']);
            $table->dropColumn([
                'family_id',
                'status',
                'status_message',
                'status_updated_at',
                'status_expires_at',
            ]);
        });
    }
};
