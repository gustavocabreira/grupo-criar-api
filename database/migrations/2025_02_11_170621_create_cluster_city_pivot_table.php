<?php

use App\Models\City;
use App\Models\Cluster;
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
        Schema::create('cluster_city_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cluster::class);
            $table->foreignIdFor(City::class);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_city_pivot');
    }
};
