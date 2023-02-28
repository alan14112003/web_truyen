<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontPage\HistoryController;
use App\Http\Controllers\FrontPage\HomeController;
use App\Http\Controllers\FrontPage\StarController;
use App\Models\Category;
use App\Models\Story;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

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


Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'logining')->name('logining');

    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registering')->name('registering');

    Route::get('/auth/redirect/{provider}', 'redirect')->name('auth.redirect');
    Route::get('/auth/callback/{provider}', 'callback')->name('auth.callback');

    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/tim-truyen-nang-cao', 'advancedSearch')->name('advanced_search');
    Route::get('/the-loai/{slug}', 'showCategories')->name('show_categories');
    Route::get('/xep-hang', 'showRank')->name('show_rank');
    Route::get('/lich-su', 'showHistory')->name('show_history');
    Route::get('/truyen/{slug}', 'showStory')->name('show_story');
    Route::get('/truyen/{slug}/chuong-{number}', 'showChapter')->name('show_chapter');
    Route::get('/thong-tin-nguoi-dang-{id}', 'showInfo')->name('show_info');
});

Route::post('star/create/{story}', [StarController::class, 'create'])->name('star.create');

Route::post('/history/destroy', [HistoryController::class, 'destroy'])->name('history.destroy');

Route::post('/call_delete', function (){
    return view('call_delete');
 });

Route::get('/test', function (){
   return sys_get_temp_dir();
});


// Trang chủ
Breadcrumbs::for('index', function(BreadcrumbTrail $trail) {
    $trail->push('Trang chủ', route('index'));
});

// Trang thể loại
Breadcrumbs::for('show_categories', function(BreadcrumbTrail $trail, Category $category) {
    $trail->parent('index');
    $trail->push(ucfirst(strtolower($category->name)), route('show_categories', $category->slug));
});

// Trang xếp hạng
Breadcrumbs::for('show_rank', function(BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Xếp hạng lượt xem', route('show_rank'));
});
// Trang lich sử
Breadcrumbs::for('show_history', function(BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Lịch sử đọc truyện', route('show_history'));
});
// Trang tìm truyện nâng cao
Breadcrumbs::for('advanced_search', function(BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Tìm truyện nâng cao', route('advanced_search'));
});

//trang truyện
Breadcrumbs::for('show_story', function(BreadcrumbTrail $trail, Story $story) {
    $trail->parent('index');
    $trail->push(ucfirst(strtolower($story->name)), route('show_story', $story->slug));
});
