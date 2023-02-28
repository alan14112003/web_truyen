<?php

namespace App\Models;

use App\Enums\ChapterPinEnum;
use App\Enums\StoryLevelEnum;
use App\Enums\StoryPinEnum;
use App\Enums\StoryStatusEnum;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'author_id',
        'author_2_id',
        'descriptions',
        'level',
        'pin',
        'user_id',
        'image',
        'slug',
    ];

    protected $casts = [
        'status' => 'integer',
        'author_id' => 'integer',
        'author_2_id' => 'integer',
        'level' => 'integer',
        'pin' => 'integer',
        'user_id' => 'integer',
    ];

    protected $appends = [
        'categories_name',
        'categories_link',
        'image_url',
        'level_name',
        'status_name',
        'pin_name'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function author() {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }

    public function author_2() {
        return $this->belongsTo(Author::class, 'author_2_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_story',
            'story_id', 'category_id');
    }

    public function chapter()
    {
        return $this->hasMany(Chapter::class);
    }

    public function view()
    {
        return $this->hasMany(View::class);
    }

    public function star(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Star::class);
    }

    public function getCategoriesNameAttribute()
    {
        return implode(', ', $this->categories->pluck('name')->toArray());
    }

    public function getCategoriesLinkAttribute()
    {
        $arrCategories = $this->categories()->get(['name', 'slug'])->toArray();

        $arrLinkCategories = array_map(function ($category){
            return "<a href='" . route('show_categories', $category['slug']) . "'>" . $category['name'] . "</a>";
        }, $arrCategories);

        return implode(', ', $arrLinkCategories);
    }

    public function getImageUrlAttribute()
    {
        return asset("storage/$this->image");
    }
    public function getLevelNameAttribute()
    {
        return StoryLevelEnum::getNameByValue($this->level);
    }
    public function getStatusNameAttribute()
    {
        return StoryStatusEnum::getNameByValue($this->status);
    }
    public function getPinNameAttribute()
    {
        return StoryPinEnum::getNameByValue($this->pin);
    }
}
