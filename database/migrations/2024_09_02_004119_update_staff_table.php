<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('phone_number')->unique()->change();
            $table->string('email')->unique()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('phone_number')->unique(false)->change();
            $table->string('email')->unique(false)->change();
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
