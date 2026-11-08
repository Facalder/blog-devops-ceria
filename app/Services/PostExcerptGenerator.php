<?php

namespace App\Services;

class PostExcerptGenerator
{
    private const ELLIPSIS = '…';

    public function __construct(private readonly MarkdownRenderer $markdown) {}

    public function generate(string $markdown, int $limit = 160): string
    {
        $text = $this->plainText($markdown);

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim($this->cutAtWordBoundary($text, $limit), ' .,;:!?-').self::ELLIPSIS;
    }

    private function cutAtWordBoundary(string $text, int $limit): string
    {
        $cut = mb_substr($text, 0, $limit);
        $cutsInsideWord = mb_substr($text, $limit, 1) !== ' ';
        $lastSpace = mb_strrpos($cut, ' ');

        if ($cutsInsideWord && $lastSpace !== false) {
            return mb_substr($cut, 0, $lastSpace);
        }

        return $cut;
    }

    private function plainText(string $markdown): string
    {
        $html = $this->markdown->render($markdown);
        $decoded = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', strip_tags($decoded)));
    }
}
