<?php

use App\Models\Post;
use Illuminate\Console\Scheduling\Schedule;

test('command publishes scheduled posts that are due', function () {
    $scheduledAt = now()->subMinutes(5)->startOfMinute();
    $post = Post::factory()->scheduled($scheduledAt)->create();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('1 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->scheduled_at)->toBeNull()
        ->and($post->published_at->toDateTimeString())->toBe($scheduledAt->toDateTimeString());
});

test('command does not publish posts scheduled in the future', function () {
    $post = Post::factory()->scheduled(now()->addHour())->create();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('0 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    $post->refresh();

    expect($post->status)->toBe('scheduled')
        ->and($post->published_at)->toBeNull()
        ->and($post->scheduled_at)->not->toBeNull();
});

test('command is safe to run repeatedly', function () {
    $post = Post::factory()->scheduled(now()->subMinutes(10)->startOfMinute())->create();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('1 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    $publishedAt = $post->fresh()->published_at->toDateTimeString();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('0 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->published_at->toDateTimeString())->toBe($publishedAt)
        ->and($post->scheduled_at)->toBeNull();
});

test('command publishes every due post and leaves the rest', function () {
    $duePosts = Post::factory()->count(3)->scheduled(now()->subMinute())->create();
    $futurePost = Post::factory()->scheduled(now()->addDay())->create();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('3 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    foreach ($duePosts as $duePost) {
        expect($duePost->fresh()->status)->toBe('published');
    }

    expect($futurePost->fresh()->status)->toBe('scheduled');
});

test('command publishes a post scheduled for exactly now', function () {
    $post = Post::factory()->scheduled(now())->create();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    expect($post->fresh()->status)->toBe('published');
});

test('command ignores drafts and posts that are already published', function () {
    $draft = Post::factory()->draft()->create();
    $published = Post::factory()->create(['published_at' => now()->subDay()->startOfMinute()]);
    $publishedAt = $published->published_at->toDateTimeString();

    $this->artisan('posts:publish-scheduled')
        ->expectsOutput('0 artikel terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    expect($draft->fresh()->status)->toBe('draft')
        ->and($published->fresh()->status)->toBe('published')
        ->and($published->fresh()->published_at->toDateTimeString())->toBe($publishedAt);
});

test('command is registered in the scheduler to run every minute', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command, 'posts:publish-scheduled'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('* * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});
