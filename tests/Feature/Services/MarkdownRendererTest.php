<?php

use App\Services\MarkdownRenderer;

beforeEach(function () {
    $this->renderer = new MarkdownRenderer;
});

test('renders headings and emphasis', function () {
    $html = $this->renderer->render("## Judul\n\n**tebal** dan *miring*");

    expect($html)
        ->toContain('<h2>Judul</h2>')
        ->toContain('<strong>tebal</strong>')
        ->toContain('<em>miring</em>');
});

test('turns a single Enter into a line break', function () {
    $html = $this->renderer->render("baris satu\nbaris dua");

    expect($html)->toContain('baris satu<br />')->toContain('baris dua');
});

test('keeps a blank line as a paragraph separator', function () {
    $html = $this->renderer->render("paragraf satu\n\nparagraf dua");

    expect(substr_count($html, '<p>'))->toBe(2);
});

test('does not render raw script tags', function () {
    $html = $this->renderer->render("<script>alert('xss')</script>");

    expect($html)->not->toContain('<script');
});

test('does not keep inline html with event handlers', function () {
    $html = $this->renderer->render('Halo <img src=x onerror=alert(1)> dunia');

    expect($html)->not->toContain('<img');
});

test('drops javascript links', function () {
    $html = $this->renderer->render('[klik saya](javascript:alert(1))');

    expect($html)->not->toContain('javascript:');
});

test('keeps normal https links', function () {
    $html = $this->renderer->render('[Laravel](https://laravel.com)');

    expect($html)->toContain('href="https://laravel.com"');
});

test('does not format markdown inside code blocks', function () {
    $html = $this->renderer->render("```\n**bukan tebal**\n```");

    expect($html)->toContain('**bukan tebal**')->not->toContain('<strong>');
});

test('returns an empty string for empty content', function () {
    expect($this->renderer->render(''))->toBe('');
});
