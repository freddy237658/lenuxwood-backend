<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('facebook_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('x_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['facebook_url', 'tiktok_url', 'linkedin_url', 'x_url']);
        });
    }
};