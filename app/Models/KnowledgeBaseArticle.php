<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'keywords',
        'tag_id'
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
