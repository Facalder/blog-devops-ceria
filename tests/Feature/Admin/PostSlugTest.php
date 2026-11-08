<?php

use App\Livewire\Admin\Posts\Create;
use App\Livewire\Admin\Posts\Edit;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('creating a post with an existing title gets a unique slug', function () {
    $user = User::factory()->create();
    Post::factory()->create(['slug' => 'panduan-devops']);

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Panduan DevOps')
        ->set('content', 'Isi artikel.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'Panduan DevOps',
        'slug' => 'panduan-devops-2',
    ]);
});

test('a manually typed slug that is already taken is still rejected', function () {
    $user = User::factory()->create();
    Post::factory()->create(['slug' => 'panduan-devops']);

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Judul Lain')
        ->set('slug', 'panduan-devops')
        ->set('content', 'Isi artikel.')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

test('a title with only symbols still produces a valid slug', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', '!!! ???')
        ->set('content', 'Isi artikel.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('posts', ['title' => '!!! ???', 'slug' => 'artikel']);
});

test('editing a post with an emptied slug generates a unique one', function () {
    $user = User::factory()->create();
    Post::factory()->create(['slug' => 'judul-edit-baru']);
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('title', 'Judul Edit Baru')
        ->set('slug', '')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'slug' => 'judul-edit-baru-2',
    ]);
});

test('slug follows the title even when the whole title arrives at once', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'judul')
        ->set('title', 'judul tes 123')
        ->assertSet('slug', 'judul-tes-123');
});

test('slug preview is already unique while typing', function () {
    $this->actingAs(User::factory()->create());
    Post::factory()->create(['slug' => 'judul']);

    Livewire::test(Create::class)
        ->set('title', 'Judul')
        ->assertSet('slug', 'judul-2');
});

test('slug stops following the title after the author edits it', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('title', 'Judul Awal')
        ->set('slug', 'slug-pilihan')
        ->set('title', 'Judul Baru')
        ->assertSet('slug', 'slug-pilihan');
});

test('slug follows the title again after it is cleared', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('slug', 'slug-pilihan')
        ->set('slug', '')
        ->set('title', 'Judul Baru')
        ->assertSet('slug', 'judul-baru');
});
