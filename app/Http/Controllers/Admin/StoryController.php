<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChapterPinEnum;
use App\Enums\StoryLevelEnum;
use App\Enums\StoryPinEnum;
use App\Enums\StoryStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class StoryController extends Controller
{
    private Builder $model;
    private string $table;
    private string $title;

    public function __construct()
    {
        $this->model = Story::query();
        $this->table = (new Story())->getTable();

        View::share('table', $this->table);
    }


    public function index(Request $request)
    {
        $q = $request->get('q');
        $categoriesFilter = $request->get('categories');
        $statusFilter = $request->get('status');
        $levelFilter = $request->get('level');
        $pinFilter = $request->get('pin');
        $usersFilter = $request->get('users');

        $query = $this->model
            ->with('categories')
//            ->addSelect('*',
//                DB::raw('(select count(*) FROM `chapters`
//                WHERE `stories`.`id` = `chapters`.`story_id` and chapters.pin > '. StoryPinEnum::EDITING .' ) AS `chapter_count`'))
            ->with('chapter', function ($qr) {
                $qr->where('pin', '>', ChapterPinEnum::EDITING);
            })
            ->with('author')
            ->with('user')
            ->latest()
            ->where('name', 'like', "%$q%")
            ->where('pin', '>',StoryPinEnum::EDITING)
        ;


        if (isset($categoriesFilter) && !in_array('All', $categoriesFilter)) {
            $query = $query->whereHas('categories', function($qr) use ($categoriesFilter) {
                $qr->whereIn('categories.id', $categoriesFilter);
            });
        }

        if (isset($levelFilter) && $levelFilter !== 'All') {
            $query = $query->where('level', $levelFilter);
        }

        if (isset($statusFilter) && $statusFilter !== 'All') {
            $query = $query->where('status', $statusFilter);
        }

        if (isset($pinFilter) && $pinFilter !== 'All') {
            $query = $query->where('pin', $pinFilter);
        }

        if (isset($usersFilter) && $usersFilter !== 'All') {
            $query = $query->where('user_id', $usersFilter);
        }


        $data = $query->paginate();


        //        categories
        $categories = Category::query()->get();

        //        status
        $statusEnum = StoryStatusEnum::getValues();
        $status = [];
        foreach ($statusEnum as $item) {
            $status[$item] = StoryStatusEnum::getNameByValue($item);
        }

        //        level
        $levelEnum = StoryLevelEnum::getValues();
        $level = [];
        foreach ($levelEnum as $item) {
            $level[$item] = StoryLevelEnum::getNameByValue($item);
        }

        //        pin
        $pinEnum = StoryPinEnum::getValues();
        $pin = [];
        foreach ($pinEnum as $item) {
            if ($item === StoryPinEnum::EDITING) continue;
            if ($item === StoryPinEnum::NOT_APPROVE) continue;
            $pin[$item] = StoryPinEnum::getNameByValue($item);
        }

//        user
        $users = Story::selectRaw("DISTINCT users.id, users.name")
            ->join('users', 'stories.user_id', '=', 'users.id')
            ->where('pin', '>', StoryPinEnum::EDITING)
            ->get()
        ;

//        dd(implode(', ', $query->categories->pluck('name')->toArray()));

        $this->title = 'Danh sách truyện';
        View::share('title', $this->title);

        return view("admin.$this->table.index", [
            'data' => $data,
            'q' => $q,

            'categories' => $categories,
            'status' => $status,
            'level' => $level,
            'pin' => $pin,
            'users' => $users,

            'categoriesFilter' => $categoriesFilter,
            'statusFilter' => $statusFilter,
            'levelFilter' => $levelFilter,
            'pinFilter' => $pinFilter,
            'usersFilter' => $usersFilter,
        ]);
    }

    public function view($id)
    {
        $stories = $this->model
            ->where('pin', '>', StoryPinEnum::EDITING)
            ->get(['id', 'name']);

        $query = Story::selectRaw('COUNT(chapters.story_id) as chapter_count,
         stories.id, stories.name, stories.status, stories.level, stories.author_id, stories.author_2_id,
         stories.pin, stories.descriptions, stories.image,
         author.name as author
          ')
            ->join('authors as author', 'stories.author_id', '=', 'author.id')

            ->join('chapters', 'chapters.story_id', '=', 'stories.id')
            ->where('chapters.pin', '>', ChapterPinEnum::EDITING)
            ->where('stories.id', $id)
            ->groupBy('stories.id')
        ;
        if (!is_null($query->first()->author_2_id)) {
            $query->addSelect(DB::raw('author_2.name as author_2'))
                ->join('authors as author_2', 'stories.author_2_id', '=', 'author_2.id');
        }
        $story = $query->first();

        $categories = Category::selectRaw('`categories`.*, `category_story`.`story_id` as `pivot_story_id`,
                                `category_story`.`category_id` as `pivot_category_id`')
            ->join('category_story', 'categories.id', '=', 'category_story.category_id')
            ->where('category_story.story_id', $id)
            ->get();

        $arrCategoriesId = $categories->pluck('id')->toArray();
        $arrCategoriesName = $categories->pluck('name')->toArray();

        $arrLinkCategories = array_map(function ($id, $name){
            return "<a href='" . $id . "'>" . $name . "</a>";
        }, $arrCategoriesId, $arrCategoriesName);

        $arrLinkCategories = implode(', ', $arrLinkCategories);

        $chapters = Chapter::query()
            ->where('pin', '>', ChapterPinEnum::EDITING)
            ->where('story_id', $id)
            ->paginate(5);

        $this->title = "$story->name";
        View::share('title', $this->title);

        return view("admin.$this->table.view", [
            'story' => $story,
            'stories' => $stories,
            'chapters' => $chapters,
            'arrLinkCategories' => $arrLinkCategories,
        ]);
    }

    public function find(Request $request)
    {
        $story_id = $request->get('story_id');
        return redirect()->route("admin.$this->table.view", $story_id);
    }

    public function approve($id)
    {
        $this->model->find($id)->update([
            'pin' => StoryPinEnum::APPROVED,
        ]);
        Chapter::query()->where('story_id', $id)
            ->where('pin', ChapterPinEnum::UPLOADING)
            ->update([
                'pin' => ChapterPinEnum::APPROVED,
            ]);
        ;
        return redirect()->back()->with('success', 'Đã duyệt truyện');
    }

    public function un_approve($id)
    {
        $this->model->find($id)->update([
            'pin' => StoryPinEnum::NOT_APPROVE,
        ]);
        Chapter::query()->where('story_id', $id)
            ->where('pin', '>', ChapterPinEnum::EDITING)
            ->update([
                'pin' => ChapterPinEnum::UPLOADING,
            ]);
        ;
        return redirect()->back()->with('success', 'Đã bỏ duyệt truyện');
    }

    public function pinned($id)
    {
        $story = $this->model->find($id);
        if ($story->pin === StoryPinEnum::PINNED) {
            $story->update([
                'pin' => StoryPinEnum::APPROVED,
            ]);
            return redirect()->back()->with('success', 'Đã bỏ ghim truyện');
        } else {
            $story->update([
                'pin' => StoryPinEnum::PINNED,
            ]);
            Chapter::query()->where('story_id', $id)
                ->where('pin', ChapterPinEnum::UPLOADING)
                ->update([
                    'pin' => ChapterPinEnum::APPROVED,
                ]);
            ;
        }
        return redirect()->back()->with('success', 'Đã ghim truyện');
    }

}
