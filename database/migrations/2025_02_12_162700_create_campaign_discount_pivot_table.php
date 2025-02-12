<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_discount_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Campaign::class);
            $table->foreignIdFor(Discount::class);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_discount_pivot');
    }
};
