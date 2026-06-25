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
       Schema::create('dp_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('character_id');
            $table->integer('admin_id');
            $table->integer('amount');
            $table->string('reason')->nullable()->default(null);
            $table->text('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fp_logs');
    }
};
