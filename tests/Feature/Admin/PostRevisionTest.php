<?php

declare(strict_types=1);

use App\Exceptions\DataTamperingException;
use App\Livewire\Admin\Posts\Edit;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostRevision;
use App\Models\User;
use App\Services\PostRevisionService;
use Livewire\Livewire;

test('editing post creates revision snapshot in database', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Versi Awal',
        'content' => 'Konten versi pertama.',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('title', 'Versi Kedua')
        ->set('content', 'Konten versi kedua yang sudah diperbaiki.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('post_revisions', [
        'post_id' => $post->id,
        'title' => 'Versi Kedua',
        'content' => 'Konten versi kedua yang sudah diperbaiki.',
    ]);
});

test('saving post without changing content does not create duplicate revision', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Konten Tetap',
        'content' => 'Tidak ada perubahan apa pun.',
    ]);

    $service = new PostRevisionService;
    $rev1 = $service->createRevision($post, $user, 'Initial');
    $rev2 = $service->createRevision($post, $user, 'Second without change');

    expect($rev1)->not->toBeNull()
        ->and($rev2)->toBeNull()
        ->and(PostRevision::where('post_id', $post->id)->count())->toBe(1);
});

test('user can view diff between revision and current content in livewire component', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Artikel DevOps',
        'content' => 'Implementasi CI/CD.',
    ]);

    $service = new PostRevisionService;
    $revision = $service->createRevision($post, $user, 'Baseline');

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('content', 'Implementasi CI/CD dan DevSecOps modern.')
        ->call('viewDiff', $revision->id)
        ->assertSet('showRevisionModal', true)
        ->assertSet('selectedRevisionId', $revision->id)
        ->assertSeeHtml('<ins');
});

test('user can rollback post to previous revision restoring fields', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Judul Lama',
        'content' => 'Konten lama penting.',
        'category_id' => $category->id,
    ]);

    $service = new PostRevisionService;
    $oldRevision = $service->createRevision($post, $user, 'Snapshot lama');

    $post->update([
        'title' => 'Judul Baru Yang Salah',
        'content' => 'Konten salah tulis.',
        'category_id' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->call('restoreRevision', $oldRevision->id)
        ->assertSet('title', 'Judul Lama')
        ->assertSet('content', 'Konten lama penting.');

    $post->refresh();
    expect($post->title)->toBe('Judul Lama')
        ->and($post->content)->toBe('Konten lama penting.')
        ->and($post->category_id)->toBe($category->id);
});

test('rollback creates new audit snapshot marking restoration', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Versi Satu',
        'content' => 'Konten awal.',
    ]);

    $service = new PostRevisionService;
    $firstRev = $service->createRevision($post, $user, 'Versi #1');

    $post->update(['content' => 'Versi kedua yang rusak']);
    $service->createRevision($post, $user, 'Versi #2');

    $service->rollback($post, $firstRev, $user);

    $latestRev = $post->revisions()->orderByDesc('revision_number')->first();

    expect($latestRev)->not->toBeNull()
        ->and($latestRev->change_summary)->toBe("Rollback ke versi #{$firstRev->revision_number}")
        ->and($latestRev->content)->toBe('Konten awal.');
});

test('rollback throws security exception when revision data has been tampered', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $service = new PostRevisionService;
    $revision = $service->createRevision($post, $user, 'Original');

    expect($revision)->not->toBeNull();

    PostRevision::where('id', $revision->id)->update([
        'content' => 'Konten telah dimanipulasi secara ilegal di database!',
    ]);

    $tamperedRevision = PostRevision::findOrFail($revision->id);

    expect($service->verifyIntegrity($tamperedRevision))->toBeFalse();

    $service->rollback($post, $tamperedRevision, $user);
})->throws(DataTamperingException::class);

test('revision pruning cleans up old records exceeding threshold', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    for ($i = 1; $i <= 20; $i++) {
        PostRevision::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'revision_number' => $i,
            'title' => "Revisi {$i}",
            'slug' => "revisi-{$i}",
            'content' => "Konten revisi ke-{$i}",
            'change_summary' => "Revisi {$i}",
            'checksum' => hash('sha256', "test-{$i}"),
        ]);
    }

    expect(PostRevision::where('post_id', $post->id)->count())->toBe(20);

    $service = new PostRevisionService;
    $deletedCount = $service->pruneOldRevisions($post, 10);

    expect($deletedCount)->toBe(10)
        ->and(PostRevision::where('post_id', $post->id)->count())->toBe(10)
        ->and(PostRevision::where('post_id', $post->id)->min('revision_number'))->toBe(11)
        ->and(PostRevision::where('post_id', $post->id)->max('revision_number'))->toBe(20);
});