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
        Schema::table('gift_user', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'ignored'])->default('pending')->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('gift_user', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
