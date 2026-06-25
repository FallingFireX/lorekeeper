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
        Schema::create('art_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('character_id');
            $table->integer('staff_id');
            $table->string('url')->nullable();
            $table->text('external_url')->nullable()->default(null);
            $table->text('notes')->nullable()->default(null);
            $table->text('staff_comments')->nullable()->default(null);
            $table->enum('status', ['Draft', 'Pending', 'Approved', 'Rejected'])->nullable()->default(null);
            $table->timestamps();
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->json('trackers')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('art_submissions');
    }
};
