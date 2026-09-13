<?php

/*
|--------------------------------------------------------------------------
| Browse Routes
|--------------------------------------------------------------------------
|
| Routes for pages that don't require being logged in to view,
| specifically the information pages.
|
*/

/**************************************************************************************************
    Widgets
**************************************************************************************************/

Route::get('items/{id}', 'Users\InventoryController@getStack');
Route::get('items/character/{id}', 'Users\InventoryController@getCharacterStack');
Route::get('items/guild/{id}', 'Guilds\InventoryController@getGuildStack');

/**************************************************************************************************
    News
**************************************************************************************************/
// PROFILES
Route::group(['prefix' => 'news'], function () {
    Route::get('/', 'NewsController@getIndex');
    Route::get('{id}.{slug?}', 'NewsController@getNews');
    Route::get('{id}.', 'NewsController@getNews');
});

/**************************************************************************************************
    Sales
**************************************************************************************************/
// PROFILES
Route::group(['prefix' => 'sales'], function () {
    Route::get('/', 'SalesController@getIndex');
    Route::get('{id}.{slug?}', 'SalesController@getSales');
    Route::get('{id}.', 'SalesController@getSales');
});

/**************************************************************************************************
    Users
**************************************************************************************************/
Route::get('/users', 'BrowseController@getUsers');
Route::get('/blacklist', 'BrowseController@getBlacklist');
Route::get('/deactivated-list', 'BrowseController@getDeactivated');

// PROFILES
Route::group(['prefix' => 'user', 'namespace' => 'Users'], function () {
    Route::get('{name}/gallery', 'UserController@getUserGallery');
    Route::get('{name}/favorites', 'UserController@getUserFavorites');
    Route::get('{name}/favorites/own-characters', 'UserController@getUserOwnCharacterFavorites');

    Route::get('{name}', 'UserController@getUser');
    Route::get('{name}/aliases', 'UserController@getUserAliases');
    Route::get('{name}/characters', 'UserController@getUserCharacters');
    Route::get('{name}/sublist/{key}', 'UserController@getUserSublist');
    Route::get('{name}/myos', 'UserController@getUserMyoSlots');
    Route::get('{name}/inventory', 'UserController@getUserInventory');
    Route::get('{name}/bank', 'UserController@getUserBank');

    Route::get('{name}/currency-logs', 'UserController@getUserCurrencyLogs');
    Route::get('{name}/item-logs', 'UserController@getUserItemLogs');
    Route::get('{name}/ownership', 'UserController@getUserOwnershipLogs');
    Route::get('{name}/submissions', 'UserController@getUserSubmissions');
    Route::get('{name}/queue-submissions', 'UserController@getUserQueueSubmissions');
});

/**************************************************************************************************
    Characters
**************************************************************************************************/
Route::get('/masterlist', 'BrowseController@getCharacters');
Route::get('/myos', 'BrowseController@getMyos');
Route::get('/sublist/{key}', 'BrowseController@getSublist');
Route::group(['prefix' => 'character', 'namespace' => 'Characters'], function () {
    Route::get('{slug}', 'CharacterController@getCharacter');
    Route::get('{slug}/profile', 'CharacterController@getCharacterProfile');
    Route::get('{slug}/bank', 'CharacterController@getCharacterBank');
    Route::get('{slug}/inventory', 'CharacterController@getCharacterInventory');
    Route::get('{slug}/images', 'CharacterController@getCharacterImages');

    Route::get('{slug}/currency-logs', 'CharacterController@getCharacterCurrencyLogs');
    Route::get('{slug}/item-logs', 'CharacterController@getCharacterItemLogs');
    Route::get('{slug}/ownership', 'CharacterController@getCharacterOwnershipLogs');
    Route::get('{slug}/change-log', 'CharacterController@getCharacterLogs');
    Route::get('{slug}/submissions', 'CharacterController@getCharacterSubmissions');

    Route::get('{slug}/gallery', 'CharacterController@getCharacterGallery');
});
Route::group(['prefix' => 'myo', 'namespace' => 'Characters'], function () {
    Route::get('{id}', 'MyoController@getCharacter');
    Route::get('{id}/profile', 'MyoController@getCharacterProfile');
    Route::get('{id}/ownership', 'MyoController@getCharacterOwnershipLogs');
    Route::get('{id}/change-log', 'MyoController@getCharacterLogs');
});

/**************************************************************************************************
    World
**************************************************************************************************/

Route::group(['prefix' => 'world'], function () {
    Route::get('/', 'WorldController@getIndex');

    Route::get('currencies', 'WorldController@getCurrencies');
    Route::get('rarities', 'WorldController@getRarities');
    Route::get('species', 'WorldController@getSpecieses');
    Route::get('subtypes', 'WorldController@getSubtypes');
    Route::get('species/{id}/traits', 'WorldController@getSpeciesFeatures');
    Route::get('species/{speciesId}/trait/{id}', 'WorldController@getSpeciesFeatureDetail')->where(['id' => '[0-9]+', 'speciesId' => '[0-9]+']);
    Route::get('item-categories', 'WorldController@getItemCategories');
    Route::get('items', 'WorldController@getItems');
    Route::get('items/{id}', 'WorldController@getItem');
    Route::get('trait-categories', 'WorldController@getFeatureCategories');
    Route::get('traits', 'WorldController@getFeatures');
    Route::get('character-categories', 'WorldController@getCharacterCategories');
});

Route::group(['prefix' => 'prompts'], function () {
    Route::get('/', 'PromptsController@getIndex');
    Route::get('prompt-categories', 'PromptsController@getPromptCategories');
    Route::get('prompts', 'PromptsController@getPrompts');
    Route::get('{id}', 'PromptsController@getPrompt');
});

Route::group(['prefix' => 'shops'], function () {
    Route::get('/', 'ShopController@getIndex');
    Route::get('{id}', 'ShopController@getShop')->where(['id' => '[0-9]+']);
    Route::get('{id}/{stockId}', 'ShopController@getShopStock')->where(['id' => '[0-9]+', 'stockId' => '[0-9]+']);
});

Route::group(['prefix' => 'queues'], function () {
    Route::get('/', 'QueuesController@getIndex');
    Route::get('queue-categories', 'QueuesController@getQueueCategories');
    Route::get('queues', 'QueuesController@getQueues');
    Route::get('{id}', 'QueuesController@getQueue');
    Route::get('index/{key}', 'QueuesController@getQueueIndexPage');
});

/**************************************************************************************************
    Site Pages
**************************************************************************************************/
Route::get('credits', 'PageController@getCreditsPage');
Route::get('info/{key}', 'PageController@getPage');

/**************************************************************************************************
    Raffles
**************************************************************************************************/
Route::group(['prefix' => 'raffles'], function () {
    Route::get('/', 'RaffleController@getRaffleIndex');
    Route::get('view/{id}', 'RaffleController@getRaffleTickets');
});

/**************************************************************************************************
    Submissions
**************************************************************************************************/
Route::group(['prefix' => 'submissions', 'namespace' => 'Users'], function () {
    Route::get('view/{id}', 'SubmissionController@getSubmission');
});
Route::group(['prefix' => 'claims', 'namespace' => 'Users'], function () {
    Route::get('view/{id}', 'SubmissionController@getClaim');
});

/**************************************************************************************************
    Comments
**************************************************************************************************/
Route::get('comment/{id}', 'PermalinkController@getComment');

/**************************************************************************************************
    Galleries
**************************************************************************************************/
Route::group(['prefix' => 'gallery'], function () {
    Route::get('/', 'GalleryController@getGalleryIndex');
    Route::get('all', 'GalleryController@getAll');
    Route::get('{id}', 'GalleryController@getGallery');
    Route::get('view/{id}', 'GalleryController@getSubmission');
    Route::get('view/favorites/{id}', 'GalleryController@getSubmissionFavorites');
});

/**************************************************************************************************
    Reports
**************************************************************************************************/
Route::group(['prefix' => 'reports', 'namespace' => 'Users'], function () {
    Route::get('/bug-reports', 'ReportController@getBugIndex');
});

/**************************************************************************************************
    Queue Submissions
**************************************************************************************************/
Route::group(['prefix' => 'queue-submissions', 'namespace' => 'Users'], function () {
    Route::get('view/{id}', 'QueueSubmissionController@getSubmission');
});

/**************************************************************************************************
    Guilds
**************************************************************************************************/
Route::group(['prefix' => __('guilds.guilds'), 'namespace' => 'Guilds'], function () {
    Route::get('/', 'GuildController@getGuildIndex');

    Route::group(['prefix' => '{id}'], function () {
        Route::get('/', 'GuildController@getGuild');

        Route::get('edit', 'GuildController@getGuildEdit');
        Route::post('edit', 'GuildController@postGuildEdit');
        Route::post('edit/staff', 'GuildController@postGuildStaffEdit');

        Route::get('edit-ranks', 'GuildController@getGuildEditRanks');
        Route::post('edit-ranks', 'GuildController@postGuildEditRanks');
        Route::get('manage-members', 'GuildController@getManageMembers');
        Route::post('manage-members', 'GuildController@postEditManageMembers');

        Route::group(['prefix' => 'shop'], function () {
            Route::get('/', 'GuildController@getGuildShop');
            Route::get('create', 'GuildController@getGuildShopCreateEdit');
            Route::post('create', 'GuildController@postCreateEditShop');

            Route::get('edit', 'GuildController@getGuildShopCreateEdit');
            Route::post('edit', 'GuildController@postCreateEditShop');

            //Route::get('edit/stock', 'GuildController@getStockModal');
            Route::post('edit/stock', 'GuildController@postEditShopStock');
            Route::post('buy', 'GuildController@postBuy');
            Route::get('{shopId}/{stockId}', 'GuildController@getShopStock')->where(['id' => '[0-9]+', 'stockId' => '[0-9]+']);
        });

        Route::group(['prefix' => 'bank'], function () {
            Route::get('/', 'GuildController@getGuildBank');
            Route::post('transfer', 'GuildController@postCurrencyTransfer');
        });

        Route::group(['prefix' => 'members'], function () {
            Route::get('/', 'GuildController@getGuildMembers');
            Route::get('add', 'GuildController@getGuildAddMembersModal');
            Route::post('add', 'GuildController@postGuildAddMembers');
            Route::post('remove', 'GuildController@postGuildRemoveMembers');
        });

        Route::group(['prefix' => 'characters'], function () {
            Route::get('/', 'GuildController@getGuildCharacters');
            Route::get('add', 'GuildController@getGuildAddCharactersModal');
            Route::post('add', 'GuildController@postGuildAddCharacters');
            Route::post('remove', 'GuildController@postGuildRemoveCharacters');
        });

        Route::group(['prefix' => 'inventory'], function () {
            Route::get('/', 'GuildController@getGuildInventory');
            Route::post('edit', 'InventoryController@postEdit');
        });

        Route::get(strtolower(__('guilds.playpen')), 'GuildController@getGuildPets');
        Route::get('armory', 'GuildController@getGuildArmory');

        Route::post('disband', 'GuildController@postDisbandGuild');
        Route::post('invite/{action}', 'GuildController@postGuildInvitationAction')->where('action', 'accept|reject');
    });
});
