<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phivolcs_earthquakes', function (Blueprint $table) {
            $table->id();

            $table->dateTime('occurred_at')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->double('magnitude')->nullable();
            $table->double('depth_km')->nullable();
            $table->string('location_text')->nullable();

            $table->text('source_url')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('fetched_at');
            $table->string('hash')->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phivolcs_earthquakes');
    }
};
