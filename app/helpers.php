<?php


use App\Enums\ChapterPinEnum;
use App\Enums\StoryPinEnum;
use App\Models\Category;
use App\Models\Story;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;

if (!function_exists('chapterList')) {
    function categoryList(): Collection|array
    {
        return Category::query()->get();
    }
}

if (!function_exists('replaceCategories')) {
    function replaceCategories($categories): array
    {
        $newCategories = [];
        foreach ($categories as $category) {
            $newCategories[] = [
                'name' => $category['name'],
                'slug' => $category['slug']
            ];
        }
        return $newCategories;
    }
}

if (!function_exists('handleResponseAPI')) {
    function handleResponseAPI($message, $status = false, $body = null): \Illuminate\Http\JsonResponse
    {   $slugStoryUrl = Route::current()->parameter('slug') ?? ':slug';
        $links = [
            'list_stories' => [
                'url' => route('api.stories.list'),
                'descriptions' => 'Trả về danh sách truyện từ mới đến cũ, nếu có query thì trả về truyện với tên theo query'
            ],
            'pin_stories' => [
                'url' => route('api.stories.pin'),
                'descriptions' => 'Trả về những truyện được ghim'
            ],
            'show_stories' => [
                'url' => route('api.stories.show', ['slug' => $slugStoryUrl]),
                'descriptions' => "Trả về thông tin của một truyện nào đó, thay thế \"$slugStoryUrl\" thành slug của truyện"
            ],
            'search_stories' => [
                'url' => route('api.stories.search'),
                'descriptions' => 'Trả về thông tin của những truyện với tên có query trong đó'
            ],
            'ads_stories' => [
                'url' => route('api.stories.advancedSearch'),
                'descriptions' => 'Trả về danh sách truyện với những thông tin tìm kiếm nâng cao và bộ lọc nâng cao'
            ]
        ];

        $checkSelf = false;
//        Thay đổi 1 url hiện tại thành self
        foreach ($links as $key => $link) {
            if ($link['url'] === url()->current()) {
                $links['self'] = $link;
                unset($links[$key]);
                $checkSelf = true;
            }
        }

//        đẩy self lên đầu để cho nó hiện cho hợp lý
        if ($checkSelf) {
            $links = [
                'self' => $links['self'],
                ...$links
            ];
        }

        return response()->json([
            "status" => $status,
            "message" => $message,
            "body" => $body,
            "links" => $links,
        ]);
    }
}


if (!function_exists('listStoriesSearch')) {
    function listStoriesSearch(): Collection|array
    {
        $stories = Story::query()
            ->with('categories')
            ->addSelect("*")
            ->selectSub("select authors.name from authors where authors.id = stories.author_id", 'author_name')
            ->selectSub("
            select number
            from chapters
            where story_id = stories.id and pin = ". ChapterPinEnum::APPROVED ."
            order by number desc limit 1
            ", 'chapter_new')
            ->where('stories.pin', '>', StoryPinEnum::UPLOADING)
            ->inRandomOrder()
            ->get();
        $listStories = [];
        foreach ($stories as $item) {
            $story = [];
            $story['id'] = $item->id;
            $story['name'] = $item->name;
            $story['chapter_new'] = $item->chapter_new;
            $story['category_name'] = $item->categoriesName;
            $story['author'] = $item->author_name;
            $story['image'] = $item->image_url;
            $story['slug'] = $item->slug;
            $listStories[] = $story;
        }
        return $listStories;
    }
}
