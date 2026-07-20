<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuggestionsTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();         
            $table->integer('category_id')->unsigned();    
            $table->string('title');                        
            $table->text('text');
            $table->timestamps();
        });

        Schema::create('suggestions_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->boolean('is_visible');
            $table->timestamps();
        });

        //labels are tags that can be added
        Schema::create('suggestions_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color', 10)->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('suggestions_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('suggestion_id');
            $table->integer('label_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');

        Schema::dropIfExists('suggestions_category');

        Schema::dropIfExists('suggestions_labels');
    }
};
