<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_admin_role', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned(); //User ID
            $table->integer('team_id')->unsigned();
            $table->enum('type', ['Lead', 'Primary', 'Secondary', 'Trainee'])->nullable()->default('Primary'); //Role Rank (this is 1, 2, 3 or 4 in order of seniority)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_admin_role');
    }
};
