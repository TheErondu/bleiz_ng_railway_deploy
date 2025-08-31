<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->decimal('roi_accrued', 12, 2)->default(0);
            $table->decimal('roi_withdrawn', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn(['roi_accrued', 'roi_withdrawn']);
        });
    }
};
