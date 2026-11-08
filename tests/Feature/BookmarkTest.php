<?php

use App\Livewire\BookmarkButton;
use App\Livewire\Bookmarks\Index;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('user can add a bookmark', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);

    Livewire::test(BookmarkButton::class, ['post' => $post])
        ->assertSet('bookmarked', false)
        ->call('toggle')
        ->assertSet('bookmarked', true);

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    expect($user->isBookmarked($post))->toBeTrue();
});

test('user can remove a bookmark', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $user->toggleBookmark($post);

    $this->actingAs($user);

    Livewire::test(BookmarkButton::class, ['post' => $post])
        ->assertSet('bookmarked', true)
        ->call('toggle')
        ->assertSet('bookmarked', false);

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    expect($user->isBookmarked($post))->toBeFalse();
});

test('bookmarked article appears on the bookmarks page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['title' => 'Belajar GitHub Actions']);
    $user->toggleBookmark($post);

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee('Belajar GitHub Actions');
});

test('draft article is hidden from the bookmarks page', function () {
    $user = User::factory()->create();
    $draft = Post::factory()->draft()->create(['title' => 'Artikel Draf Rahasia']);
    $user->toggleBookmark($draft);

    $this->actingAs($user)
        ->get(route('bookmarks.index'))
        ->assertOk()
        ->assertDontSee('Artikel Draf Rahasia');

    expect($user->getBookmarkedPosts())->toBeEmpty();
});

test('user can remove a bookmark from the bookmarks page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $user->toggleBookmark($post);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('remove', $post->id);

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('user cannot save the same article twice', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $user->toggleBookmark($post);
    expect($user->bookmarks()->count())->toBe(1);

    $insertDuplicate = fn () => DB::table('bookmarks')->insert([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    expect($insertDuplicate)->toThrow(QueryException::class);
    expect($user->bookmarks()->count())->toBe(1);
});

test('guest cannot bookmark an article', function () {
    $post = Post::factory()->create();

    Livewire::test(BookmarkButton::class, ['post' => $post])
        ->call('toggle')
        ->assertRedirect(route('login'));

    $this->assertDatabaseCount('bookmarks', 0);
});

test('guest is redirected from the bookmarks page', function () {
    $this->get(route('bookmarks.index'))
        ->assertRedirect(route('login'));
});

test('user cannot see bookmarks of another user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->create(['title' => 'Bookmark Milik Owner']);
    $owner->toggleBookmark($post);

    $this->actingAs($other)
        ->get(route('bookmarks.index'))
        ->assertOk()
        ->assertDontSee('Bookmark Milik Owner');

    expect($other->getBookmarkedPosts())->toBeEmpty();
});

test('user cannot remove the bookmark of another user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->create();
    $owner->toggleBookmark($post);

    $this->actingAs($other);

    Livewire::test(Index::class)
        ->call('remove', $post->id);

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $owner->id,
        'post_id' => $post->id,
    ]);
});
