<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('guilds', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->default('Unknown');
            $table->integer('owner_id')->nullable();
            $table->string('status')->default('inactive');
            $table->boolean('is_disbanded')->default(0);
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->string('location')->nullable();
            $table->integer('reputation')->default(0);
            $table->integer('max_users')->nullable();
            $table->integer('max_characters')->nullable();
            $table->boolean('open_new_users')->default(1);
            $table->boolean('automatic_app_approval')->default(0);
            $table->boolean('open_inventory')->default(0);
            $table->boolean('open_bank')->default(0);
            $table->boolean('open_pets')->default(0);
            $table->boolean('open_armory')->default(0);
            $table->boolean('has_logo')->default(0);
            $table->boolean('has_banner')->default(0);
            $table->timestamps();
        });

        Schema::create('guild_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('guild_id');
            $table->integer('user_id');
            $table->integer('permissions')->default(0);
            $table->string('rank_id');
            $table->integer('reputation')->default(0);
            $table->timestamp('joined_at', precision: 0)->nullable();

            $table->primary(['guild_id', 'user_id']);
        });

        Schema::create('guild_characters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('guild_id');
            $table->integer('character_id');
            $table->string('rank');
            $table->integer('reputation')->default(0);
            $table->timestamp('joined_at', precision: 0)->nullable();

            $table->primary(['guild_id', 'character_id']);
        });

        Schema::create('guild_currencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('guild_id');
            $table->integer('currency_id');
            $table->integer('quantity')->default(1);

            $table->primary(['guild_id', 'currency_id']);
        });

        Schema::create('guild_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_id');
            $table->text('stack_name')->nullable();
            $table->integer('guild_id');
            $table->integer('count')->default(1);
            $table->text('data')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at', precision: 0)->nullable();
        });

        Schema::create('guild_pets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_id');
            $table->integer('pet_id');
            $table->timestamp('joined_at', precision: 0)->nullable();
            $table->integer('affection')->default(0);
        });

        Schema::create('guild_gear', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_id');
            $table->integer('gear_id');
            $table->timestamp('attached_at', precision: 0)->nullable();
            $table->timestamp('deleted_at', precision: 0)->nullable();
        });

        Schema::create('guild_weapons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_id');
            $table->integer('weapon_id');
            $table->timestamp('attached_at', precision: 0)->nullable();
            $table->timestamp('deleted_at', precision: 0)->nullable();
        });

        Schema::create('guild_applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_id');
            $table->integer('user_id');
            $table->text('comment')->nullable();
            $table->text('guild_comment')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        Schema::create('guild_shops', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_id');
            $table->string('name')->nullable();
            $table->boolean('has_image')->default(0);
            $table->longtext('description')->nullable();
            $table->longtext('parsed_description')->nullable();
            $table->boolean('is_active')->default(0);
        });

        Schema::create('guild_shop_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_shop_id');
            $table->string('guild_id');
            $table->integer('user_id');
            $table->integer('character_id')->nullable();
            $table->integer('item_id');
            $table->integer('currency_id');
            $table->integer('cost');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::create('guild_shop_stock', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('guild_shop_id');
            $table->integer('item_id');
            $table->integer('currency_id');
            $table->boolean('guild_only')->default(false);
            $table->float('cost')->default(1.0);
            $table->float('guild_cost')->nullable();
            $table->text('data')->nullable();
            $table->integer('quantity')->default(1);
            $table->boolean('is_limited_stock')->default(false);
            $table->integer('purchase_limit')->nullable();
            $table->string('stock_type')->default('Item');
            $table->boolean('is_visible')->default(0);
        });

        Schema::create('guild_ranks', function (Blueprint $table) {
            $table->id();
            $table->integer('guild_id');
            $table->string('name');
            $table->integer('level')->default(0);
            $table->integer('required_reputation')->default(0);
            $table->text('description')->nullable();
            $table->boolean('for_character')->default(1);
            $table->boolean('for_user')->default(1);
            $table->boolean('has_image')->default(0);
            $table->string('hash', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('guilds');
        Schema::dropIfExists('guild_users');
        Schema::dropIfExists('guild_characters');
        Schema::dropIfExists('guild_currencies');
        Schema::dropIfExists('guild_items');
        Schema::dropIfExists('guild_pets');
        Schema::dropIfExists('guild_gear');
        Schema::dropIfExists('guild_weapons');
        Schema::dropIfExists('guild_applications');
        Schema::dropIfExists('guild_shops');
        Schema::dropIfExists('guild_shop_log');
        Schema::dropIfExists('guild_shop_stock');
        Schema::dropIfExists('guild_ranks');
    }
};
