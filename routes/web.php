<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['viewcachebuster', 'admindebugbar']], function () {

    // Catch for hard-coded /home route in RedirectsUsers.php
    Route::get('home', 'SiteController@home');

    Route::get('credits', 'SiteController@credits')->name('misc.credits');

    Route::get('about', 'SiteController@about')->name('misc.about');

    Route::get('privacy', 'SiteController@privacy')->name('legal.privacy');

    Route::get('terms', 'SiteController@terms')->name('legal.terms');

    Route::get('cookies', 'SiteController@cookies')->name('legal.cookies');

    Route::get('/', 'SiteController@index')->name('home');

    Route::get('changelog', 'SiteController@changelog')->name('misc.changelog');

    Route::get('mapping', 'SiteController@mapping')->name('misc.mapping');

    Route::get('affixes', 'SiteController@affixes')->name('misc.affixes');

    Route::get('timetest', 'SiteController@timetest')->name('misc.timetest');

    Route::get('looptest', 'SiteController@looptest')->name('misc.looptest');

    Route::get('status', 'SiteController@status')->name('misc.status');

    Route::get('login/google', 'Auth\GoogleLoginController@redirectToProvider')->name('login.google');
    Route::get('login/google/callback', 'Auth\GoogleLoginController@handleProviderCallback')->name('login.google.callback');

    Route::get('login/battlenet', 'Auth\BattleNetLoginController@redirectToProvider')->name('login.battlenet');
    Route::get('login/battlenet/callback', 'Auth\BattleNetLoginController@handleProviderCallback')->name('login.battlenet.callback');

    Route::get('login/discord', 'Auth\DiscordLoginController@redirectToProvider')->name('login.discord');
    Route::get('login/discord/callback', 'Auth\DiscordLoginController@handleProviderCallback')->name('login.discord.callback');

    Route::get('try', 'DungeonRouteController@try')->name('dungeonroute.try');
    Route::post('try', 'DungeonRouteController@try')->name('dungeonroute.try.post');

    // ['auth', 'role:admin|user']

    Route::get('patreon-unlink', 'PatreonController@unlink')->name('patreon.unlink');
    Route::get('patreon-link', 'PatreonController@link')->name('patreon.link');
    Route::get('patreon-oauth', 'PatreonController@oauth_redirect')->name('patreon.oauth.redirect');

    Route::get('profile/(user}', 'ProfileController@view')->name('profile.view');

    Route::post('userreport/new', 'UserReportController@store')->name('userreport.new');

    Route::get('dungeonroutes', 'SiteController@dungeonroutes');
    Route::get('routes', 'DungeonRouteController@list')->name('dungeonroutes');

    // May be accessed without being logged in
    Route::get('team/invite/{invitecode}', 'TeamController@invite')->name('team.invite');

    Route::group(['middleware' => ['auth', 'role:user|admin']], function () {
        // Must be logged in to create a new dungeon route
        Route::get('new', 'DungeonRouteController@new')->name('dungeonroute.new');
        Route::post('new', 'DungeonRouteController@savenew')->name('dungeonroute.savenew');

        Route::post('new/mdtimport', 'MDTImportController@import')->name('dungeonroute.new.mdtimport');

        // Legacy redirects
        Route::get('edit/{dungeonroute}', 'DungeonRouteController@editLegacy');
        Route::patch('edit/{dungeonroute}', 'DungeonRouteController@updateLegacy');

        // Edit your own dungeon routes
        Route::get('{dungeonroute}/edit', 'DungeonRouteController@edit')->name('dungeonroute.edit');
        // Submit a patch for your own dungeon route
        Route::patch('{dungeonroute}/edit', 'DungeonRouteController@update')->name('dungeonroute.update');
        // Clone a route
        Route::get('{dungeonroute}/clone', 'DungeonRouteController@clone')->name('dungeonroute.clone');

        Route::get('profile', 'ProfileController@edit')->name('profile.edit');
        Route::patch('profile/{user}', 'ProfileController@update')->name('profile.update');
        Route::patch('profile/{user}/privacy', 'ProfileController@updatePrivacy')->name('profile.updateprivacy');
        Route::patch('profile', 'ProfileController@changepassword')->name('profile.changepassword');

        Route::get('teams', 'TeamController@list')->name('team.list');
        Route::get('team/new', 'TeamController@new')->name('team.new');
        Route::get('team/{team}', 'TeamController@edit')->name('team.edit');
        Route::delete('team/{team}', 'TeamController@delete')->name('team.delete');

        Route::post('team/new', 'TeamController@savenew')->name('team.savenew');
        Route::patch('team/{team}', 'TeamController@update')->name('team.update');
        Route::get('team/invite/{invitecode}/accept', 'TeamController@inviteaccept')->name('team.invite.accept');
    });

    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        // Only admins may view a list of profiles
        Route::get('profiles', 'ProfileController@list')->name('profile.list');

        // Dungeons
        Route::get('admin/dungeon/new', 'DungeonController@new')->name('admin.dungeon.new');
        Route::get('admin/dungeon/{dungeon}', 'DungeonController@edit')->name('admin.dungeon.edit');

        Route::post('admin/dungeon/new', 'DungeonController@savenew')->name('admin.dungeon.savenew');
        Route::patch('admin/dungeon/{dungeon}', 'DungeonController@update')->name('admin.dungeon.update');

        Route::get('admin/dungeons', 'DungeonController@list')->name('admin.dungeons');

        // Floors
        Route::get('admin/floor/new', 'FloorController@new')->name('admin.floor.new')->where(['dungeon' => '[0-9]+']);
        Route::get('admin/floor/{floor}', 'FloorController@edit')->name('admin.floor.edit');

        Route::post('admin/floor/new', 'FloorController@savenew')->name('admin.floor.savenew')->where(['dungeon' => '[0-9]+']);
        Route::patch('admin/floor/{floor}', 'FloorController@update')->name('admin.floor.update');

        // Expansions
        Route::get('admin/expansion/new', 'ExpansionController@new')->name('admin.expansion.new');
        Route::get('admin/expansion/{expansion}', 'ExpansionController@edit')->name('admin.expansion.edit');

        Route::post('admin/expansion/new', 'ExpansionController@savenew')->name('admin.expansion.savenew');
        Route::patch('admin/expansion/{expansion}', 'ExpansionController@update')->name('admin.expansion.update');

        Route::get('admin/expansions', 'ExpansionController@list')->name('admin.expansions');

        // NPCs
        Route::get('admin/npc/new', 'NpcController@new')->name('admin.npc.new');
        Route::get('admin/npc/{npc}', 'NpcController@edit')->name('admin.npc.edit');

        Route::post('admin/npc/new', 'NpcController@savenew')->name('admin.npc.savenew');
        Route::patch('admin/npc/{npc}', 'NpcController@update')->name('admin.npc.update');

        Route::get('admin/npcs', 'NpcController@list')->name('admin.npcs');

        Route::get('admin/users', 'UserController@list')->name('admin.users');
        Route::post('admin/user/{user}/makeadmin', 'UserController@makeadmin')->name('admin.user.makeadmin');
        Route::post('admin/user/{user}/makeuser', 'UserController@makeuser')->name('admin.user.makeuser');

        Route::get('admin/userreports', 'UserReportController@list')->name('admin.userreports');

        Route::get('admin/tools', 'AdminToolsController@index')->name('admin.tools');

        Route::get('admin/tools/mdt/string', 'AdminToolsController@mdtview')->name('admin.tools.mdt.string.view');
        Route::post('admin/tools/mdt/string', 'AdminToolsController@mdtviewsubmit')->name('admin.tools.mdt.string.submit');
        Route::get('admin/tools/mdt/diff', 'AdminToolsController@mdtdiff')->name('admin.tools.mdt.diff');

        Route::get('admin/tools/datadump/exportdungeondata', 'ExportDungeonDataController@submit')->name('admin.tools.datadump.exportdungeondata');
    });


    Route::group(['prefix' => 'ajax', 'middleware' => 'ajax'], function () {
        Route::get('/{publickey}/data', 'APIDungeonRouteController@data');

        Route::get('/routes', 'APIDungeonRouteController@list');

        Route::post('/mdt/details', 'MDTImportController@details')->name('mdt.details');

        Route::post('/profile/legal', 'APIProfileController@legalAgree');

        Route::group(['prefix' => 'echo', 'middleware' => ['auth', 'role:user']], function () {
            Route::get('{dungeonroute}/members', 'APIEchoController@members');
        });

        Route::group(['prefix' => '{dungeonroute}'], function () {
            Route::post('/brushline', 'APIBrushlineController@store');
            Route::delete('/brushline/{brushline}', 'APIBrushlineController@delete');

            Route::post('/killzone', 'APIKillZoneController@store');
            Route::delete('/killzone/{killzone}', 'APIKillZoneController@delete');

            Route::post('/mapcomment', 'APIMapCommentController@store');
            Route::delete('/mapcomment/{mapcomment}', 'APIMapCommentController@delete');

            Route::post('/path', 'APIPathController@store');
            Route::delete('/path/{path}', 'APIPathController@delete');

            Route::post('/raidmarker/{enemy}', 'APIEnemyController@setRaidMarker');

            Route::patch('/', 'APIDungeonRouteController@store')->name('api.dungeonroute.update');
            Route::delete('/', 'APIDungeonRouteController@delete')->name('api.dungeonroute.delete');

            Route::post('/favorite', 'APIDungeonRouteController@favorite')->name('api.dungeonroute.favorite');
            Route::delete('/favorite', 'APIDungeonRouteController@favoriteDelete')->name('api.dungeonroute.favorite.delete');

            Route::post('/publish', 'APIDungeonRouteController@publish')->name('api.dungeonroute.publish');

            Route::post('/rate', 'APIDungeonRouteController@rate')->name('api.dungeonroute.rate');
            Route::delete('/rate', 'APIDungeonRouteController@rateDelete')->name('api.dungeonroute.rate.delete');
        });

        // Teams
        Route::post('/team/{team}/changerole', 'APITeamController@changeRole');
        Route::post('/team/{team}/route/{dungeonroute}', 'APITeamController@addRoute');
        Route::delete('/team/{team}/member/{user}', 'APITeamController@removeMember');
        Route::delete('/team/{team}/route/{dungeonroute}', 'APITeamController@removeRoute');

        Route::group(['middleware' => ['auth', 'role:admin']], function () {
            Route::post('/enemy', 'APIEnemyController@store');
            Route::delete('/enemy/{enemy}', 'APIEnemyController@delete');

            Route::post('/enemypack', 'APIEnemyPackController@store');
            Route::delete('/enemypack/{enemypack}', 'APIEnemyPackController@delete');

            Route::post('/enemypatrol', 'APIEnemyPatrolController@store');
            Route::delete('/enemypatrol/{enemypatrol}', 'APIEnemyPatrolController@delete');

            Route::post('/dungeonfloorswitchmarker', 'APIDungeonFloorSwitchMarkerController@store')->where(['floor_id' => '[0-9]+']);
            Route::delete('/dungeonfloorswitchmarker/{dungeonfloorswitchmarker}', 'APIDungeonFloorSwitchMarkerController@delete');

            Route::post('/dungeonstartmarker', 'APIDungeonStartMarkerController@store')->where(['dungeon' => '[0-9]+']);
            Route::delete('/dungeonstartmarker/{dungeonstartmarker}', 'APIDungeonStartMarkerController@delete');

            Route::post('/userreport/{userreport}/markasresolved', 'APIUserReportController@markasresolved');

            Route::post('/tools/mdt/diff/apply', 'AdminToolsController@applychange');
        });
    });

    // View any dungeon route (catch all)
    Route::get('{dungeonroute}', 'DungeonRouteController@view')
        ->name('dungeonroute.view');
    // Preview of a route for image capturing library
    Route::get('{dungeonroute}/preview/{floorindex}', 'DungeonRouteController@preview')
        ->name('dungeonroute.preview');
});