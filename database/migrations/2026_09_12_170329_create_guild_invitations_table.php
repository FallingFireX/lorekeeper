<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('guild_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('guild_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();

            $table->unique(['guild_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('guild_invitations');
    }
};
