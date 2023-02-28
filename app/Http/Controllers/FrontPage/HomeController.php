<?php

namespace App\Http\Controllers\FrontPage;

use App\Enums\ChapterPinEnum;
use App\Enums\StoryLevelEnum;
use App\Enums\StoryPinEnum;
use App\Enums\StoryStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\History;
use App\Models\Star;
use App\Models\Story;
use App\Models\View as ViewAlias;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
//    trang chủ
    public function index(Request $request): Factory|\Illuminate\Contracts\View\View|Application
    {
        $q = $request->get('q');

//        láy ra các truyện theo thứ tự ngẫu nhiên
        $stories = Story::query()
            ->select('*')
            ->with('categories')
            ->with('author')
            ->withCount('view')
            ->selectSub("
            select number
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_number')
            ->selectSub("
            select updated_at
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_time')
            ->where('pin', '>', StoryPinEnum::UPLOADING)
            ->where('name', 'like', "%$q%")
            ->latest('updated_at')
            ->paginate(12);

//        lấy ra truyện được ghim
        $storiesPin = Story::query()
            ->select('*')
            ->selectSub("
            select number
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_number')
            ->selectSub("
            select updated_at
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_time')
            ->where('pin', '=', StoryPinEnum::PINNED)
            ->inRandomOrder()
            ->limit(10)
            ->get()
        ;

//      Lịch sử đọc truyện
        $histories = History::showHistoriesByGuest();
        if (Auth::check()) {
            $histories = History::showHistoriesByUser();
        }

//      Top 10 đánh giá
        $topFiveStars = Star::showTopFiveStar();

//      top 10 view
        $topFiveViewMonth = ViewAlias::showTopViewMonth();
        $topFiveViewWeek = ViewAlias::showTopViewWeek();
        $topFiveViewDay = ViewAlias::showTopViewDay();

        return view('page.index', [
            'q' => $q,
            'stories' => $stories,
            'storiesPin' => $storiesPin,
            'histories' => $histories,
            'topFiveStars' => $topFiveStars,
            'topFiveViewMonth' => $topFiveViewMonth,
            'topFiveViewWeek' => $topFiveViewWeek,
            'topFiveViewDay' => $topFiveViewDay,
        ]);
    }

//    trang thể loại
    public function showCategories($slug): Factory|\Illuminate\Contracts\View\View|Application
    {
        $category = Category::query()->where('slug', $slug)->first();
        $data = Story::query()
            ->select([
                'stories.*'
            ])
            ->withCount('view')
            ->selectSub("
            select number
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_number')
            ->selectSub("
            select updated_at
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_time')
            ->join('category_story as cs', 'cs.story_id', '=', 'stories.id')
            ->join('categories as c', 'c.id', '=', 'cs.category_id')
            ->where('c.slug', $slug)
            ->where('stories.pin', '>', StoryPinEnum::UPLOADING)
            ->inRandomOrder()
            ->paginate(12)
        ;

        View::share('title', "Thể loại - $category->name");

        return view('page.category', [
            'data' => $data,
            'category' => $category,
        ]);
    }

//    trang truyện
    public function showStory(Request $request,$slug): Factory|\Illuminate\Contracts\View\View|Application
    {
        $sort = $request->get('sort');

//        story
        $story = Story::query()
            ->with('categories')
            ->with('author')
            ->with('chapter', function ($qr) {
                $qr->where('pin', ChapterPinEnum::APPROVED);
            })
            ->where('slug', $slug)
            ->where('pin', '>', StoryPinEnum::UPLOADING)
            ->first()
        ;
//        số sao
        $stars = Star::query()->where('story_id', $story->id)->get();
        $starTotal = 0;
        $starPerson = count($stars);
        $starAvg = 0;
        if ($starPerson !== 0) {
            foreach ($stars as $star) {
                $starTotal += $star->total;
            }
            $starAvg = round( ( ($starTotal / ($starPerson * 5) ) * 5), 1);
        }

//        chapters
        $query = Chapter::query()
            ->where('pin', ChapterPinEnum::APPROVED)
            ->where('story_id', $story->id);

        if (isset($sort)) {
            $query->orderBy('number', $sort);
        }
        $chapters = $query->get();

        View::share('title', ucfirst($story->name));

        return view('page.story', [
            'story' => $story,
            'chapters' => $chapters,
            'sort' => $sort,
            'starAvg' => $starAvg,
            'starPerson' => $starPerson,
        ]);
    }

//    trang chương truyện
    public function showChapter(Request $request, $slug, $number): Factory|\Illuminate\Contracts\View\View|Application
    {
        $story = Story::query()->where('slug', $slug)->first();

//        thêm vào lich sử
        History::createHistory($story->id, $number);

//      lấy ra trang hiện tại
        $chapter = Chapter::query()
            ->where('story_id', $story->id)
            ->where('pin', ChapterPinEnum::APPROVED)
            ->where('number', $number)->first();


//        thêm vào view
        $user_id = $request->ip();
        if (Auth::check()) {
            $user_id = Auth::id();
        }
        ViewAlias::createView($user_id, $story->id, $chapter->id);

//      lấy ra danh sách trang
        $chapterList = Chapter::query()
            ->where('pin', ChapterPinEnum::APPROVED)
            ->where('story_id', $story->id)
            ->pluck('number')
            ->toArray()
        ;

//        lấy ra các con trỏ trang
        $curent = array_search($chapter->number, $chapterList, true);
        $pre = $chapterList[$curent - 1] ?? '';
        $next = $chapterList[$curent + 1] ?? '';
        $first = reset($chapterList);
        $last = end($chapterList);

        View::share('title', "Chương $chapter->number - " . ucfirst($story->name));

        return view('page.chapter', [
            'story' => $story,
            'chapter' => $chapter,
            'chapterList' => $chapterList,
            'pre' => $pre,
            'next' => $next,
            'first' => $first,
            'last' => $last,
        ]);
    }

//    trang tìm truyện nâng cao
    public function advancedSearch(Request $request): Factory|\Illuminate\Contracts\View\View|Application
    {
        $q = $request->get('q');
        $categoriesFilter = $request->get('categories');
        $statusFilter = $request->get('status');
        $levelFilter = $request->get('level');
        $authorFilter = $request->get('author');

        $query = Story::query()
            ->with('categories')
            ->with('author')
            ->withCount('view')
            ->selectSub("
            select number
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_number')
            ->selectSub("
            select updated_at
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new_time')
            ->inRandomOrder()
            ->where('name', 'like', "%$q%")
            ->where('pin', '>',StoryPinEnum::UPLOADING)
        ;


        if (isset($categoriesFilter) && !in_array('All', $categoriesFilter)) {
            foreach($categoriesFilter as $category) {
                $query = $query->whereHas('categories', function($qr) use ($category) {
                    $qr->where('categories.id', $category);
                });
            }
        }

        if (isset($levelFilter) && $levelFilter !== 'All') {
            $query = $query->where('level', $levelFilter);
        }

        if (isset($statusFilter) && $statusFilter !== 'All') {
            $query = $query->where('status', $statusFilter);
        }

        if (isset($authorFilter) && $authorFilter !== 'All') {
            $query = $query->where('author_id', $authorFilter);
        }


        $data = $query->paginate(12);


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

//        author
        $authors = Story::query()->selectRaw("DISTINCT authors.id, authors.name")
            ->join('authors', 'stories.author_id', '=', 'authors.id')
            ->where('pin', '>', StoryPinEnum::UPLOADING)
            ->get()
        ;

        View::share('title', 'Tìm truyện nâng cao');

        return view("page.advanced_search", [
            'data' => $data,
            'q' => $q,

            'categories' => $categories,
            'status' => $status,
            'level' => $level,
            'authors' => $authors,

            'categoriesFilter' => $categoriesFilter,
            'statusFilter' => $statusFilter,
            'levelFilter' => $levelFilter,
            'authorFilter' => $authorFilter,
        ]);
    }

//    trang xếp hạng truyện theo view
    public function showRank(): Factory|\Illuminate\Contracts\View\View|Application
    {
        $data = ViewAlias::showTopViewAll();

        View::share('title', 'Top truyện xếp hạng');

        return view('page.show_rank', [
            'data' => $data,
        ]);
    }

    public function showHistory(): Factory|\Illuminate\Contracts\View\View|Application
    {
        $histories = History::showHistoriesByGuest();
        if (Auth::check()) {
            $histories = History::showHistoriesByUser();
        }

        View::share('title', 'Lịch sử đọc truyện');

        return view('page.show_history', [
            'histories' => $histories,
        ]);
    }
}
