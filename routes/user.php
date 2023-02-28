<?php


use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ChapterController;
use App\Http\Controllers\User\StoryController;
use App\Http\Controllers\User\UserController;
use App\Models\Chapter;
use App\Models\Story;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

Route::get('/nguoi-dung/', [UserController::class, 'index'])->name('index');

Route::prefix('the-loai')->name('categories.')
    ->controller(CategoryController::class)->group(function() {
    Route::get('/', 'index')->name('index');
});
Route::controller(UserController::class)->name('info.')
    ->group(function() {

        Route::get('/thong-tin-ca-nhan/sua/{id}', 'edit')->name('edit');
        Route::put('/thong-tin-ca-nhan/sua/{id}', 'update')->name('update');
        Route::delete('/thong-tin-ca-nhan/xoa-anh', 'deleteAvatar')->name('delete_avatar');
        Route::post('/thong-tin-ca-nhan/sua-anh', 'changeAvatar')->name('change_avatar');
    });

// truyện của tôi
Route::prefix('truyen-cua-toi')->name('stories.')->group(function() {
    Route::controller(StoryController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/truyen-da-xoa', 'blackList')->name('black_list');

        Route::get('/find', 'find')->name('find');
        Route::get('/{slug}-{story}', 'show')->name('show')
            ->where([
                'slug' => '^(?!((.*/)|(create$))).*\D+.*$',
                'story' => '[0-9]+',
            ]);

        Route::get('/them', 'create')->name('create');
        Route::post('/them', 'store')->name('store');

        Route::get('/sua/{id}', 'edit')->name('edit');
        Route::put('/sua/{id}', 'update')->name('update');

        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
        Route::post('/approve/{id}', 'approve')->name('approve');
        Route::post('/pinned/{id}', 'pinned')->name('pinned');

        Route::post('/restore/{id}', 'restore')->name('restore');
        Route::delete('/kill/{id}', 'kill')->name('kill');

        Route::post('/upload/{id}', 'upload')->name('upload');
    });

    Route::controller(ChapterController::class)->name('chapters.')
        ->group(function() {
        Route::get('/{slug}/chuong/them', 'create')->name('create');
        Route::post('/{slug}/chuong/them', 'store')->name('store');

        Route::get('/{slug}/chuong-{number}', 'show')->name('show');

        Route::get('/{slug}/chuong/sua/{id}', 'edit')->name('edit');
        Route::put('/{slug}/chuong/sua/{id}', 'update')->name('update');

        Route::post('/{slug}/chapter/upload/{id}', 'upload')->name('upload');
        Route::post('/{slug}/chapter/upload_all', 'uploadAll')->name('upload_all');

        Route::delete('/{slug}/chapter/destroy/{number}', 'destroy')->name('destroy');
    });

});
Route::prefix('chuong')->name('chapters.')->controller(ChapterController::class)
->group(function () {
    Route::get('/chuong-khong-duoc-duyet', 'index')->name('index');
});





// trang chủ
Breadcrumbs::for('user.index', function(BreadcrumbTrail $trail) {
    $trail->parent('index');
    $trail->push('Tổng hợp truyện', route('user.index'));
});

// Thể loại
Breadcrumbs::for('user.categories.index', function(BreadcrumbTrail $trail) {
    $trail->parent('user.index');
    $trail->push('Thể loại', route('user.categories.index'));
});

// Truyện
Breadcrumbs::for('user.stories.index', function(BreadcrumbTrail $trail) {
    $trail->parent('user.index');
    $trail->push('Danh sách truyện của tôi', route('user.stories.index'));
});
Breadcrumbs::for('user.stories.create', function(BreadcrumbTrail $trail) {
    $trail->parent('user.stories.index');
    $trail->push('Thêm truyện mới', route('user.stories.create'));
});
Breadcrumbs::for('user.stories.show', function(BreadcrumbTrail $trail, Story $story) {
    $trail->parent('user.stories.index');
    $trail->push("$story->name", route('user.stories.show', [$story->slug, $story]));
});
Breadcrumbs::for('user.stories.edit', function(BreadcrumbTrail $trail, Story $story) {
    $trail->parent('user.stories.show', $story);
    $trail->push("Thay đổi thông tin truyện", route('user.stories.edit', $story->id));
});
Breadcrumbs::for('user.stories.black_list', function(BreadcrumbTrail $trail) {
    $trail->parent('user.stories.index');
    $trail->push('Danh sách truyện đã xóa', route('user.stories.black_list'));
});

// chapter

Breadcrumbs::for('user.chapters.index', function(BreadcrumbTrail $trail) {
    $trail->parent('user.index');
    $trail->push("Danh sách chương không được kiểm duyệt", route('user.chapters.index'));
});

Breadcrumbs::for('user.chapter.show', function(BreadcrumbTrail $trail, Story $story, Chapter $chapter) {
    $trail->parent('user.stories.show', $story);
    $trail->push("Chương $chapter->number", route('user.stories.chapters.show', [$story->slug, $chapter->number]));
});

Breadcrumbs::for('user.chapter.create', function(BreadcrumbTrail $trail, Story $story) {
    $trail->parent('user.stories.show', $story);
    $trail->push("Thêm chương truyện", route('user.stories.chapters.create', $story->slug));
});

Breadcrumbs::for('user.chapter.edit', function(BreadcrumbTrail $trail, Story $story, Chapter $chapter) {
    $trail->parent('user.chapter.show', $story, $chapter);
    $trail->push("Sửa chương truyện", route('user.stories.chapters.edit', [$story->slug, $chapter->id]));
});
