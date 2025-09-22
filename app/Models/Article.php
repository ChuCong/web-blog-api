<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;


class Article extends Model
{
    protected $table = 'articles';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'url',
        'active',
        'content',
        'category_id',
        'seo_key',
        'seo_title',
        'seo_description'
    ];
    protected static function booted()
    {
        static::creating(function ($article) {
            $article->slug = Str::slug($article->title);

            if (static::where('slug', $article->slug)->exists()) {
                throw new Exception('Slug đã tồn tại trong database!');
            }
        });

        static::updating(function ($article) {
            $article->slug = Str::slug($article->title);

            if (static::where('slug', $article->slug)
                ->where('id', '!=', $article->id)
                ->exists()
            ) {
                throw new Exception('Slug đã tồn tại trong database!');
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
