<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleToken extends Model
{
    protected $fillable = ['refresh_token'];

    public static function currentRefreshToken(): ?string
    {
        return static::query()->value('refresh_token');
    }
}
