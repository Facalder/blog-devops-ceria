<?php

use App\Livewire\Admin\Posts\Create;
use App\Livewire\Admin\Posts\Edit;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

function futureScheduleInput(int $hours = 24): string
{
    return now()->addHours($hours)->format('Y-m-d\TH:i');
}

test('admin can save a scheduled article', function () {
    $user = User::factory()->create();
    $scheduleInput = futureScheduleInput();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Rilis Terjadwal')
        ->set('slug', 'rilis-terjadwal')
        ->set('content', 'Isi konten terjadwal.')
        ->set('status', 'scheduled')
        ->set('scheduled_at', $scheduleInput)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $post = Post::where('slug', 'rilis-terjadwal')->firstOrFail();

    expect($post->status)->toBe('scheduled')
        ->and($post->published_at)->toBeNull()
        ->and($post->scheduled_at->format('Y-m-d\TH:i'))->toBe($scheduleInput);
});

test('admin cannot schedule an article in the past', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Jadwal Lampau')
        ->set('slug', 'jadwal-lampau')
        ->set('content', 'Isi konten.')
        ->set('status', 'scheduled')
        ->set('scheduled_at', now()->subHour()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasErrors(['scheduled_at']);

    $this->assertDatabaseMissing('posts', ['slug' => 'jadwal-lampau']);
});

test('admin cannot schedule an article for the current minute', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Jadwal Menit Ini')
        ->set('slug', 'jadwal-menit-ini')
        ->set('content', 'Isi konten.')
        ->set('status', 'scheduled')
        ->set('scheduled_at', now()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasErrors(['scheduled_at']);

    $this->assertDatabaseMissing('posts', ['slug' => 'jadwal-menit-ini']);
});

test('schedule time is required when status is scheduled', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Tanpa Waktu')
        ->set('slug', 'tanpa-waktu')
        ->set('content', 'Isi konten.')
        ->set('status', 'scheduled')
        ->set('scheduled_at', null)
        ->call('save')
        ->assertHasErrors(['scheduled_at']);

    $this->assertDatabaseMissing('posts', ['slug' => 'tanpa-waktu']);
});

test('schedule time is discarded when publishing immediately', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Terbit Langsung')
        ->set('slug', 'terbit-langsung')
        ->set('content', 'Isi konten.')
        ->set('status', 'published')
        ->set('scheduled_at', now()->subDay()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('slug', 'terbit-langsung')->firstOrFail();

    expect($post->status)->toBe('published')
        ->and($post->published_at)->not->toBeNull()
        ->and($post->scheduled_at)->toBeNull();
});

test('admin can reschedule a scheduled article', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create(['user_id' => $user->id]);
    $newScheduleInput = futureScheduleInput(72);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->assertSet('status', 'scheduled')
        ->set('scheduled_at', $newScheduleInput)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $post->refresh();

    expect($post->status)->toBe('scheduled')
        ->and($post->published_at)->toBeNull()
        ->and($post->scheduled_at->format('Y-m-d\TH:i'))->toBe($newScheduleInput);
});

test('admin cannot reschedule an article into the past', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create(['user_id' => $user->id]);
    $originalSchedule = $post->scheduled_at->toDateTimeString();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('scheduled_at', now()->subHour()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasErrors(['scheduled_at']);

    expect($post->fresh()->scheduled_at->toDateTimeString())->toBe($originalSchedule);
});

test('admin can cancel a schedule by switching the article to draft', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('status', 'draft')
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->status)->toBe('draft')
        ->and($post->scheduled_at)->toBeNull()
        ->and($post->published_at)->toBeNull();
});

test('admin can cancel a schedule with the cancel action', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->call('cancelSchedule')
        ->assertRedirect(route('posts.index'));

    $post->refresh();

    expect($post->status)->toBe('draft')
        ->and($post->scheduled_at)->toBeNull()
        ->and($post->published_at)->toBeNull();
});

test('cancel action leaves non scheduled articles untouched', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->call('cancelSchedule');

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->published_at)->not->toBeNull();
});

test('admin can publish a scheduled article immediately', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->published_at)->not->toBeNull()
        ->and($post->scheduled_at)->toBeNull();
});

test('admin can move a published article back to a schedule', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $scheduleInput = futureScheduleInput();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('status', 'scheduled')
        ->set('scheduled_at', $scheduleInput)
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->status)->toBe('scheduled')
        ->and($post->published_at)->toBeNull()
        ->and($post->scheduled_at->format('Y-m-d\TH:i'))->toBe($scheduleInput);
});

test('editing a published article keeps its original publish date', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'published_at' => now()->subDays(3)->startOfMinute(),
    ]);
    $originalPublishedAt = $post->published_at->toDateTimeString();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('title', 'Judul Diperbarui Saja')
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->status)->toBe('published')
        ->and($post->published_at->toDateTimeString())->toBe($originalPublishedAt)
        ->and($post->scheduled_at)->toBeNull();
});

test('guests cannot open the article form', function () {
    $this->get(route('posts.create'))->assertRedirect(route('login'));
});
