<?php

use App\Models\Post;
use App\Services\PostSlugGenerator;

test('generates a slug from a plain title', function () {
    expect(app(PostSlugGenerator::class)->generate('Belajar CI/CD Itu Seru'))
        ->toBe('belajar-cicd-itu-seru');
});

test('normalizes uppercase, punctuation and extra spaces', function () {
    expect(app(PostSlugGenerator::class)->generate('Halo, Dunia!!   Ini   Tes'))
        ->toBe('halo-dunia-ini-tes');
});

test('appends -2 when the slug is already taken', function () {
    Post::factory()->create(['slug' => 'judul-artikel']);

    expect(app(PostSlugGenerator::class)->generate('Judul Artikel'))
        ->toBe('judul-artikel-2');
});

test('skips suffixes that are already taken', function () {
    Post::factory()->create(['slug' => 'judul-artikel']);
    Post::factory()->create(['slug' => 'judul-artikel-2']);

    expect(app(PostSlugGenerator::class)->generate('Judul Artikel'))
        ->toBe('judul-artikel-3');
});

test('does not treat a longer slug with the same prefix as a conflict', function () {
    Post::factory()->create(['slug' => 'judul-artikel-panjang']);

    expect(app(PostSlugGenerator::class)->generate('Judul Artikel'))
        ->toBe('judul-artikel');
});

test('ignores the post being edited', function () {
    $post = Post::factory()->create(['slug' => 'judul-artikel']);

    expect(app(PostSlugGenerator::class)->generate('Judul Artikel', $post->id))
        ->toBe('judul-artikel');
});

test('falls back to a default slug when the title has only symbols', function () {
    $generator = app(PostSlugGenerator::class);

    expect($generator->generate('!!! ???'))->toBe('artikel');

    Post::factory()->create(['slug' => 'artikel']);

    expect($generator->generate('!!! ???'))->toBe('artikel-2');
});

test('keeps very long titles within the slug column limit', function () {
    $generator = app(PostSlugGenerator::class);
    $title = str_repeat('a', 255);

    $first = $generator->generate($title);
    Post::factory()->create(['slug' => $first]);
    $second = $generator->generate($title);

    expect(strlen($first))->toBeLessThanOrEqual(240)
        ->and($second)->toBe($first.'-2')
        ->and(strlen($second))->toBeLessThanOrEqual(255);
});
