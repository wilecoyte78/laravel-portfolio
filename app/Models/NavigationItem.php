<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'page_id',
        'parent_id',
        'sort_order',
        'external_url',
    ];

    protected $casts = [
        'sort_order' => 'integer',
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
        ];
    }
}
