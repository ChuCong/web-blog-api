<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'url',
        'active',
        'seo_key',
        'seo_title',
        'seo_description'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->title);

            if (static::where('slug', $category->slug)->exists()) {
                throw new Exception('Slug đã tồn tại trong database!');
            }
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->title);

            if (static::where('slug', $category->slug)
                ->where('id', '!=', $category->id)
                ->exists()
            ) {
                throw new Exception('Slug đã tồn tại trong database!');
            }
        });
    }

    public function articles()
    {
        return $this->hasMany(Article::class, "category_id", "id");
    }
     public function media()
    {
        return $this->hasOne(Media::class, "id", "media_id");
    }
}
