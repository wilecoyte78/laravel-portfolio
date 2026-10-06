<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $meta_description
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $meta_title
 * @property string|null $meta_keywords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NavigationItem> $navigationItems
 * @property-read int|null $navigation_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function navigationItems()
    {
        return $this->hasMany(NavigationItem::class);
    }
}
