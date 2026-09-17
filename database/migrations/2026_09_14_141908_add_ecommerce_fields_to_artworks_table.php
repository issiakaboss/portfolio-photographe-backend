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
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('category')->default('photos')->change();
            $table->decimal('price', 10, 2)->nullable()->after('category');
            $table->boolean('is_for_sale')->default(false)->after('price');
            $table->string('status')->default('available')->after('is_for_sale');
            $table->string('dimensions')->nullable()->after('status');
            $table->string('materials')->nullable()->after('dimensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'is_for_sale',
                'status',
                'dimensions',
                'materials',
            ]);
            $table->string('category')->default('general')->change();
        });
    }
};
