<?php

use App\Models\Post;

test('public article page does not render raw html from the author', function () {
    $post = Post::factory()->create([
        'content' => "Halo\n\n<script>alert('xss')</script>",
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertDontSee("<script>alert('xss')</script>", false);
});

test('html_content attribute uses the safe renderer', function () {
    $post = Post::factory()->make(['content' => '<img src=x onerror=alert(1)>']);

    expect($post->html_content)->not->toContain('<img');
});
