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
        Schema::create('distance', function (Blueprint $table) {
            $table->id();
            $table->string('cepIn');
            $table->string('cepFn');
            $table->float('latitude1');
            $table->float('latitude2');
            $table->float('longitude1');
            $table->float('longitude2');
            $table->float('distance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distance');
    }
};
