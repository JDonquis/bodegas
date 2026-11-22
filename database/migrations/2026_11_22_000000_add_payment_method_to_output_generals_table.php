<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('output_generals', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('total_profit');
        });
    }

    public function down(): void
    {
        Schema::table('output_generals', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
