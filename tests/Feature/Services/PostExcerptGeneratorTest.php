<?php

use App\Services\PostExcerptGenerator;

beforeEach(function () {
    $this->generator = app(PostExcerptGenerator::class);
});

test('removes markdown syntax from the excerpt', function () {
    expect($this->generator->generate("# Judul Besar\n\nIni **tebal** dan *miring*."))
        ->toBe('Judul Besar Ini tebal dan miring.');
});

test('keeps only the text of markdown links', function () {
    expect($this->generator->generate('Baca [dokumentasi Laravel](https://laravel.com) dulu'))
        ->toBe('Baca dokumentasi Laravel dulu');
});

test('turns a single Enter into a space', function () {
    expect($this->generator->generate("baris satu\nbaris dua"))
        ->toBe('baris satu baris dua');
});

test('cuts at a word boundary instead of in the middle of a word', function () {
    $content = str_repeat('kata ', 100);

    expect($this->generator->generate($content, 22))->toBe('kata kata kata kata…');
});

test('does not drop a word when the cut lands exactly on a space', function () {
    $content = str_repeat('kata ', 100);

    expect($this->generator->generate($content, 19))->toBe('kata kata kata kata…');
});

test('returns short text unchanged without an ellipsis', function () {
    expect($this->generator->generate('Singkat saja.'))->toBe('Singkat saja.');
});

test('returns an empty string for empty or whitespace-only content', function () {
    expect($this->generator->generate(''))->toBe('')
        ->and($this->generator->generate("   \n  "))->toBe('');
});

test('raw html written by the author never appears in the excerpt', function () {
    expect($this->generator->generate('Halo <img src=x onerror=alert(1)> Dunia'))
        ->toBe('Halo Dunia');
});

test('hard cuts a single very long word', function () {
    expect($this->generator->generate(str_repeat('a', 50), 10))
        ->toBe(str_repeat('a', 10).'…');
});
