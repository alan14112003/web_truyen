<?php

use App\Http\Controllers\HandleAPI\AuthController;
use App\Http\Controllers\HandleAPI\StoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// -----------URL Stories
Route::prefix('stories')
    ->name('stories.')
    ->controller(StoryController::class)
    ->group(function () {
        Route::get('/list', 'listStories')->name('list');
        Route::get('/pin', 'pinStories')->name('pin');
        Route::get('/show/{slug}', 'showStory')->name('show');
        Route::get('/search', 'searchStories')->name('search');
        Route::get('/advanced-search', 'advancedSearchStories')->name('advancedSearch');
    });

//------------------URL Authentication----------------
Route::prefix('auth')->name('auth.')
    ->controller(AuthController::class)->group(function () {
    Route::get('/redirect/{provider}', 'redirect')->name('redirect');
    Route::get('/callback/{provider}', 'callback')->name('callback');
    Route::get('test/{token}', 'test')->name('test');
});
