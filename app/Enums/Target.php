<?php

namespace App\Enums;

enum Target: string
{
    case SELF = '_self';
    case BLANK = '_blank';

    public function label(): string
    {
        return match($this) {
            self::SELF => 'Opens the link in the same window/tab. (Default)',
            self::BLANK => 'Opens the linked document in a new window/tab.',
        };
    }
}
