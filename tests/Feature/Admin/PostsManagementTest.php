<?php

use App\Livewire\Admin\Posts\Create;
use App\Livewire\Admin\Posts\Edit;
use App\Livewire\Admin\Posts\Index;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can view posts management page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertSee($post->title);
});

test('user can create a blog post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Panduan CI/CD dengan GitHub Actions')
        ->set('slug', 'panduan-cicd-github-actions')
        ->set('category_id', $category->id)
        ->set('content', '# Pengantar\n\nBerikut cara setup CI/CD.')
        ->set('excerpt', 'Panduan lengkap automasi CI/CD.')
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'Panduan CI/CD dengan GitHub Actions',
        'slug' => 'panduan-cicd-github-actions',
        'status' => 'published',
        'user_id' => $user->id,
    ]);
});

test('user can update a blog post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['post' => $post])
        ->set('title', 'Judul Artikel Terupdate')
        ->set('slug', 'judul-artikel-terupdate')
        ->set('content', 'Isi konten yang baru.')
        ->set('status', 'draft')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Judul Artikel Terupdate',
        'status' => 'draft',
    ]);
});

test('user can create a blog post without category and empty slug', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Artikel Otomatis Slug')
        ->set('slug', '')
        ->set('category_id', null)
        ->set('content', 'Konten sederhana markdown.')
        ->set('status', 'draft')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'Artikel Otomatis Slug',
        'slug' => 'artikel-otomatis-slug',
        'category_id' => null,
        'status' => 'draft',
    ]);
});

test('user cannot create a blog post without title or content', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', '')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['title', 'content']);
});

test('user can delete a blog post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $post->id);

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});
