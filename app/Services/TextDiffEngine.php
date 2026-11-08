<?php

declare(strict_types=1);

namespace App\Services;

class TextDiffEngine
{
    /**
     * @return list<array{type: 'unchanged'|'added'|'removed', value: string}>
     */
    public function calculateDiff(string $oldText, string $newText): array
    {
        if ($oldText === $newText) {
            return $oldText === '' ? [] : [['type' => 'unchanged', 'value' => $oldText]];
        }

        if ($oldText === '') {
            return [['type' => 'added', 'value' => $newText]];
        }

        if ($newText === '') {
            return [['type' => 'removed', 'value' => $oldText]];
        }

        $oldTokens = $this->tokenize($oldText);
        $newTokens = $this->tokenize($newText);

        $matrix = $this->buildLcsMatrix($oldTokens, $newTokens);
        $rawDiff = $this->backtrack($matrix, $oldTokens, $newTokens, count($oldTokens), count($newTokens));

        return $this->consolidate($rawDiff);
    }

    /**
     * @param  list<array{type: 'unchanged'|'added'|'removed', value: string}>  $diff
     */
    public function renderHtml(array $diff): string
    {
        $html = '';

        foreach ($diff as $item) {
            $escaped = htmlspecialchars($item['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            if ($item['type'] === 'added') {
                $html .= '<ins class="bg-[#D1FADF] text-[#027A48] no-underline px-1 py-0.5 rounded-[2px] font-normal">'.$escaped.'</ins>';
            } elseif ($item['type'] === 'removed') {
                $html .= '<del class="bg-[#FEE4E2] text-[#B42318] line-through px-1 py-0.5 rounded-[2px] font-normal opacity-85">'.$escaped.'</del>';
            } else {
                $html .= $escaped;
            }
        }

        return $html;
    }

    public function computeChecksum(int $postId, string $title, string $content, int $userId): string
    {
        return hash('sha256', "{$postId}|{$title}|{$content}|{$userId}");
    }

    /**
     * @param  list<array{type: 'unchanged'|'added'|'removed', value: string}>  $diff
     * @return array{added: int, removed: int, unchanged: int}
     */
    public function getStatistics(array $diff): array
    {
        $added = 0;
        $removed = 0;
        $unchanged = 0;

        foreach ($diff as $item) {
            $length = mb_strlen($item['value']);
            if ($item['type'] === 'added') {
                $added += $length;
            } elseif ($item['type'] === 'removed') {
                $removed += $length;
            } else {
                $unchanged += $length;
            }
        }

        return [
            'added' => $added,
            'removed' => $removed,
            'unchanged' => $unchanged,
        ];
    }

    /**
     * @return list<string>
     */
    private function tokenize(string $text): array
    {
        $tokens = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        return $tokens !== false ? $tokens : [$text];
    }

    /**
     * @param  list<string>  $a
     * @param  list<string>  $b
     * @return array<int, array<int, int>>
     */
    private function buildLcsMatrix(array $a, array $b): array
    {
        $m = count($a);
        $n = count($b);
        $matrix = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($a[$i] === $b[$j]) {
                    $matrix[$i + 1][$j + 1] = $matrix[$i][$j] + 1;
                } else {
                    $matrix[$i + 1][$j + 1] = max($matrix[$i + 1][$j], $matrix[$i][$j + 1]);
                }
            }
        }

        return $matrix;
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     * @param  list<string>  $a
     * @param  list<string>  $b
     * @return list<array{type: 'unchanged'|'added'|'removed', value: string}>
     */
    private function backtrack(array $matrix, array $a, array $b, int $i, int $j): array
    {
        $diff = [];

        while ($i > 0 || $j > 0) {
            if ($i > 0 && $j > 0 && $a[$i - 1] === $b[$j - 1]) {
                $diff[] = ['type' => 'unchanged', 'value' => $a[$i - 1]];
                $i--;
                $j--;
            } elseif ($j > 0 && ($i === 0 || $matrix[$i][$j - 1] >= $matrix[$i - 1][$j])) {
                $diff[] = ['type' => 'added', 'value' => $b[$j - 1]];
                $j--;
            } elseif ($i > 0 && ($j === 0 || $matrix[$i][$j - 1] < $matrix[$i - 1][$j])) {
                $diff[] = ['type' => 'removed', 'value' => $a[$i - 1]];
                $i--;
            }
        }

        return array_reverse($diff);
    }

    /**
     * @param  list<array{type: 'unchanged'|'added'|'removed', value: string}>  $raw
     * @return list<array{type: 'unchanged'|'added'|'removed', value: string}>
     */
    private function consolidate(array $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $result = [];
        $current = $raw[0];

        for ($i = 1, $count = count($raw); $i < $count; $i++) {
            if ($raw[$i]['type'] === $current['type']) {
                $current['value'] .= $raw[$i]['value'];
            } else {
                $result[] = $current;
                $current = $raw[$i];
            }
        }

        $result[] = $current;

        return $result;
    }
}
