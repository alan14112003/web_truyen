<?php

namespace App\Http\Controllers\HandleAPI;

use App\Enums\ChapterPinEnum;
use App\Enums\StoryPinEnum;
use App\Enums\StoryStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Star;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class StoryController extends Controller
{
    // [GET] /api/stories/new
    public function listStories(Request $request): \Illuminate\Http\JsonResponse
    {
//        Set hiển thị ngôn ngữ tiếng việt
        Carbon::setLocale('vi');

        $q = $request->get('q');

        // lấy ra các truyện theo thứ tự mới đến cũ
        $stories = Story::query()
            ->select('*')
            ->withCount('chapter')
            ->where('pin', '>', StoryPinEnum::UPLOADING)
            ->where('name', 'like', "%$q%")
            ->latest('updated_at')
            ->paginate(12)
            ->toArray();

        if (empty($stories['data']))
            return handleResponseAPI('Không tải được truyện');

        $newStoriesData = [];
        foreach ($stories['data'] as $story) {
            $newStoriesData[] = [
                'name' => $story['name'],
                'image' => $story['image_url'],
                'chapter' => [
                    'is_full' => StoryStatusEnum::checkStatusByValue($story['status']),
                    'count' => $story['chapter_count'],
                ],
                'categories' => replaceCategories($story['categories']),
                'slug' => $story['slug'],
                'updated_at' => Carbon::create($story['updated_at'])->diffForHumans(Carbon::now()),
            ];
        }
//            replace data stories
        $stories['data'] = $newStoriesData;

        return handleResponseAPI('Danh sách truyện từ mới đến cũ', true, $stories);
    }

    // [GET] /api/stories/pin
    public function pinStories(): \Illuminate\Http\JsonResponse
    {
        $storiesPin = Story::query()
            ->select('*')
            ->withCount('chapter')
            ->where('pin', '=', StoryPinEnum::PINNED)
            ->inRandomOrder()
            ->limit(10)
            ->get();

//        Cắt bớt thuộc tính để thay đổi đầu ra
        $newStoriesPin = [];
        foreach ($storiesPin as $story) {
            $newStoriesPin[] = [
                'name' => $story['name'],
                'image' => $story['image_url'],
                'chapter' => [
                    'is_full' => StoryStatusEnum::checkStatusByValue($story['status']),
                    'count' => $story['chapter_count'],
                ],
                'slug' => $story['slug'],
            ];
        }

        return handleResponseAPI('Hiển thị tối đa 10 truyện được ghim ngẫu nhiên',
            true,
            $newStoriesPin);
    }

    // [GET] /api/stories/show/{slug}
    public function showStory(Request $request, $slug): \Illuminate\Http\JsonResponse
    {
        try {
            $sort = $request->get('sort');

//        story
            $story = Story::query()
                ->withCount('star')
                ->withCount('view')
                ->withAvg('star', 'total')
                ->with('author')
                ->where('slug', $slug)
                ->where('pin', '>', StoryPinEnum::UPLOADING)
                ->first();

//        chapters
            $chapterQuery = Chapter::query()
                ->where('pin', ChapterPinEnum::APPROVED)
                ->where('story_id', $story->id);

            if (isset($sort)) {
                $chapterQuery->orderBy('number', $sort);
            }
            $chapters = $chapterQuery->get();

//            cắt chapter list
            $newChapter = [];
            foreach ($chapters as $chapter) {
                $newChapter[] = [
                    'name' => $chapter->name,
                    'number' => $chapter->number,
                ];
            }

//      thay đổi story
            $newStory = [
                'name' => $story['name'],
                'is_full' => StoryStatusEnum::checkStatusByValue($story['status']),
                'view_count' => $story['view_count'],
                'image' => $story['image_url'],
                'descriptions' => $story['descriptions'],
                'star' => $story['star_count'] ? [
                    'avg' => $story['star_avg_total'],
                    'count' => $story['star_count'],
                ] : null,
                'slug' => $story['slug'],
                'author' => [
                    'id' => $story['author']['id'],
                    'name' => $story['author']['name'],
                ],
                'category' => replaceCategories($story['categories']),
                'chapters' => $newChapter,
            ];

            return handleResponseAPI('Hiển thị thông tin truyện', true, $newStory);
        } catch (\Exception $e) {
            return handleResponseAPI('Có lỗi xảy ra! ' . $e->getMessage());
        }
    }

    // [GET] /api/stories/search
    public function searchStories(): \Illuminate\Http\JsonResponse
    {
        return handleResponseAPI('Không có lỗi gì cả', 'success', 'Hahaha body');
    }

    // [GET] /api/stories/advanced-search
    public function advancedSearchStories(): \Illuminate\Http\JsonResponse
    {
        return handleResponseAPI('success', 'Không có lỗi gì cả', 'Hahaha body');
    }
}
