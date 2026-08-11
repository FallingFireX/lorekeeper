<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');   //name of the team
            $table->enum('type', ['Main Team', 'Sub Team', 'Admin Accounts', 'Leadership'])->nullable()->default('Main Team');
            $table->string('relation')->nullable()->default(null);   //team relation, applicable to sub teams only, currently not in use
            $table->boolean('apps_open')->unsigned()->default(0);   //apps open, for admin applications
            $table->text('description')->nullable()->default(null);     //description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('teams');
    }
};
