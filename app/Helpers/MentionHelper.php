<?php

namespace App\Helpers;

class MentionHelper
{
    public static function render(string $text): string
    {
        return preg_replace(
            '/@([\w\s]+?)(?=\s|$|[^a-zA-Z\s])/u',
            '<span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">@$1</span>',
            $text
        );
    }
}
