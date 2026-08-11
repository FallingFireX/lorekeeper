<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('admin_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned(); //user ID
            $table->integer('team_id')->unsigned(); //Team ID (team applied to)
            $table->integer('admin_id')->unsigned()->nullable(); //admin ID (admin who accepts or rejects it)
            $table->text('application'); //application
            $table->enum('status', ['accepted', 'denied', 'pending'])->nullable()->default('pending'); //status (accept or decline)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('admin_applications');
    }
};
