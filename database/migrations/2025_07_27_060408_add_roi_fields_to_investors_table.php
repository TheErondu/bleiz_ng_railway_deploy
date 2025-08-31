<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->decimal('capital', 15, 2)->default(0);
            $table->decimal('roi_percentage', 5, 2)->default(0); // example: 12.5%
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            //
        });
    }
};
