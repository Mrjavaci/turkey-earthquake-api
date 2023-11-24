<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('earthquakes', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('time');
            $table->float('lat');
            $table->float('lng');
            $table->float('depth');
            $table->string('scale_MD');
            $table->string('scale_ML');
            $table->string('scale_Mw');
            $table->string('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earthquakes');
    }
};
