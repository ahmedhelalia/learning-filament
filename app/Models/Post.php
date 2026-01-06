<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'thumbnail',
        'title',
        'color',
        'slug',
        'category_id',
        'content',
        'tags',
        'published'
    ];

    protected $casts = [
        'tags' => 'array',
        'published' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
