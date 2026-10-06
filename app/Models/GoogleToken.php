<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $refresh_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoogleToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoogleToken extends Model
{
    protected $fillable = ['refresh_token'];

    public static function currentRefreshToken(): ?string
    {
        return static::query()->value('refresh_token');
    }
}
