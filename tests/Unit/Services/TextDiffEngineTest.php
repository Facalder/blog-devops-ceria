<?php

declare(strict_types=1);

use App\Services\TextDiffEngine;

test('diff engine returns empty list when comparing two empty strings', function () {
    $engine = new TextDiffEngine;
    $result = $engine->calculateDiff('', '');

    expect($result)->toBeEmpty();
});

test('diff engine marks identical strings as unchanged', function () {
    $engine = new TextDiffEngine;
    $result = $engine->calculateDiff('Halo dunia DevOps', 'Halo dunia DevOps');

    expect($result)->toHaveCount(1)
        ->and($result[0]['type'])->toBe('unchanged')
        ->and($result[0]['value'])->toBe('Halo dunia DevOps');
});

test('diff engine detects newly added content from empty baseline', function () {
    $engine = new TextDiffEngine;
    $result = $engine->calculateDiff('', 'Konten perdana');

    expect($result)->toHaveCount(1)
        ->and($result[0]['type'])->toBe('added')
        ->and($result[0]['value'])->toBe('Konten perdana');
});

test('diff engine detects removed content when new content is empty', function () {
    $engine = new TextDiffEngine;
    $result = $engine->calculateDiff('Konten lama yang dihapus', '');

    expect($result)->toHaveCount(1)
        ->and($result[0]['type'])->toBe('removed')
        ->and($result[0]['value'])->toBe('Konten lama yang dihapus');
});

test('diff engine accurately identifies token additions and deletions', function () {
    $engine = new TextDiffEngine;
    $result = $engine->calculateDiff('Belajar Docker bersama tim', 'Belajar Kubernetes bersama tim ceria');

    $types = array_column($result, 'type');
    expect($types)->toContain('removed')
        ->and($types)->toContain('added')
        ->and($types)->toContain('unchanged');
});

test('diff engine renders html with proper semantic tags and escape values', function () {
    $engine = new TextDiffEngine;
    $diff = [
        ['type' => 'unchanged', 'value' => 'Kode '],
        ['type' => 'removed', 'value' => '<script>alert(1)</script>'],
        ['type' => 'added', 'value' => 'Aman'],
    ];

    $html = $engine->renderHtml($diff);

    expect($html)->toContain('<ins')
        ->and($html)->toContain('<del')
        ->and($html)->not->toContain('<script>')
        ->and($html)->toContain('&lt;script&gt;');
});

test('compute checksum generates consistent sha256 hash', function () {
    $engine = new TextDiffEngine;
    $hash1 = $engine->computeChecksum(1, 'Judul', 'Konten', 42);
    $hash2 = $engine->computeChecksum(1, 'Judul', 'Konten', 42);
    $hashDifferent = $engine->computeChecksum(1, 'Judul Lain', 'Konten', 42);

    expect($hash1)->toBe($hash2)
        ->and(strlen($hash1))->toBe(64)
        ->and($hash1)->not->toBe($hashDifferent);
});

test('diff engine computes character statistics accurately', function () {
    $engine = new TextDiffEngine;
    $diff = [
        ['type' => 'unchanged', 'value' => 'Halo '],
        ['type' => 'added', 'value' => 'Dunia'],
        ['type' => 'removed', 'value' => 'Kawan'],
    ];

    $stats = $engine->getStatistics($diff);

    expect($stats['unchanged'])->toBe(5)
        ->and($stats['added'])->toBe(5)
        ->and($stats['removed'])->toBe(5);
});
