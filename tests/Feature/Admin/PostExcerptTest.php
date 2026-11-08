<?php

use App\Livewire\Admin\Posts\Create;
use App\Livewire\Admin\Posts\Edit;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('creating a post without excerpt stores a clean plain text excerpt', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'Tes Excerpt')
        ->set('content', "## Pengantar\n\nIni **penting** dan [dokumentasi](https://laravel.com).")
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::where('title', 'Tes Excerpt')->value('excerpt'))
        ->toBe('Pengantar Ini penting dan dokumentasi.');
});

test('a manually written excerpt is kept as it is', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'Excerpt Manual')
        ->set('content', 'Isi artikel yang cukup.')
        ->set('excerpt', 'Ringkasan buatan penulis.')
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::where('title', 'Excerpt Manual')->value('excerpt'))
        ->toBe('Ringkasan buatan penulis.');
});

test('a long content is cut at a word boundary with an ellipsis', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'Artikel Panjang')
        ->set('content', str_repeat('kata ', 100))
        ->call('save')
        ->assertHasNoErrors();

    $excerpt = Post::where('title', 'Artikel Panjang')->value('excerpt');

    expect($excerpt)->toEndWith('kata…')
        ->and(mb_strlen($excerpt))->toBeLessThanOrEqual(161);
});

test('raw html in the content never appears in the stored excerpt', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'Excerpt Aman')
        ->set('content', 'Halo <script>alert(1)</script> dunia')
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::where('title', 'Excerpt Aman')->value('excerpt'))
        ->not->toContain('<script');
});

test('editing a post with an emptied excerpt regenerates a clean one', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id, 'excerpt' => 'ringkasan lama']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('content', 'Isi **baru** artikel.')
        ->set('excerpt', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->excerpt)->toBe('Isi baru artikel.');
});
