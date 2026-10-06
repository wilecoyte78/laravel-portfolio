<?php

namespace App\Models;

use App\Enums\Target;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property int|null $page_id
 * @property int|null $parent_id
 * @property string|null $external_url
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $target
 * @property Target $targe
 * @property-read \Illuminate\Database\Eloquent\Collection<int, NavigationItem> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\Page|null $page
 * @property-read NavigationItem|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereExternalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NavigationItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'page_id',
        'parent_id',
        'sort_order',
        'external_url',
        'target'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'targe' => Target::class,
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * Build the full nested tree, only including published pages
     * (or items that are pure dropdown headers with no page attached).
     */
    public static function tree()
    {
        return static::roots()
            ->with(['page', 'children.page', 'children.children.page'])
            ->get()
            ->map(fn ($item) => $item->toNavArray());
    }

    public function toNavArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'url' => $this->page && $this->page->is_published
                ? '/' . $this->page->slug
                : ($this->external_url ?: null),
            'children' => $this->children->map(fn ($child) => $child->toNavArray())->values(),
            'target' => $this->target,
        ];
    }
}
