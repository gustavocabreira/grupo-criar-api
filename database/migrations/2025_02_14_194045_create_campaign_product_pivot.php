<?php

use App\Models\Campaign;
use App\Models\Product;
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
        Schema::create('campaign_product_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignidfor(Campaign::class);
            $table->foreignidfor(Product::class)->deleteOnCascade();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_product_pivot');
    }
};
