<?php

namespace App\Services;

use Illuminate\Support\Str;

class MarkdownRenderer
{
    private const OPTIONS = [
        'html_input' => 'escape',
        'allow_unsafe_links' => false,
        'renderer' => [
            'soft_break' => "<br />\n",
        ],
    ];

    public function render(string $markdown): string
    {
        return Str::markdown($markdown, self::OPTIONS);
    }
}
