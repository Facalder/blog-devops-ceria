<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('homepage hides scheduled articles and shows published ones', function () {
    Post::factory()->create(['title' => 'Artikel Terbit Biasa']);
    Post::factory()->scheduled(now()->addDay())->create(['title' => 'Artikel Terjadwal Rahasia']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Artikel Terbit Biasa')
        ->assertDontSee('Artikel Terjadwal Rahasia');
});

test('scheduled article detail url returns not found before its time', function () {
    $post = Post::factory()->scheduled(now()->addDay())->create(['slug' => 'artikel-terjadwal']);

    $this->get(route('blog.show', $post->slug))->assertNotFound();
});

test('scheduled article stays hidden even when its time passed but it is not published yet', function () {
    $post = Post::factory()->scheduled(now()->subMinute())->create(['slug' => 'menunggu-scheduler']);

    $this->get(route('blog.show', $post->slug))->assertNotFound();
});

test('article marked published with a future publish date is not public', function () {
    $post = Post::factory()->create([
        'title' => 'Terbit Masa Depan',
        'slug' => 'terbit-masa-depan',
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('blog.show', $post->slug))->assertNotFound();

    $this->get(route('home'))->assertOk()->assertDontSee('Terbit Masa Depan');
});

test('article becomes public after the command publishes it', function () {
    $post = Post::factory()->scheduled(now()->subMinute())->create([
        'title' => 'Mulai Tayang Otomatis',
        'slug' => 'mulai-tayang-otomatis',
    ]);

    $this->get(route('blog.show', $post->slug))->assertNotFound();

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('Mulai Tayang Otomatis');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Mulai Tayang Otomatis');
});

test('search does not leak scheduled articles', function () {
    Post::factory()->scheduled(now()->addDay())->create([
        'title' => 'Bocoran Kubernetes Terjadwal',
        'content' => 'Konten rahasia kubernetes.',
    ]);

    $this->get(route('home', ['search' => 'kubernetes']))
        ->assertOk()
        ->assertDontSee('Bocoran Kubernetes Terjadwal');
});

test('category with only scheduled articles is not listed on the homepage', function () {
    $category = Category::factory()->create(['name' => 'Kategori Belum Tayang']);
    Post::factory()->scheduled(now()->addDay())->create(['category_id' => $category->id]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Kategori Belum Tayang');
});

test('authenticated users also cannot open a scheduled article publicly', function () {
    $user = User::factory()->create();
    $post = Post::factory()->scheduled(now()->addDay())->create([
        'user_id' => $user->id,
        'slug' => 'jadwal-milik-sendiri',
    ]);

    $this->actingAs($user)
        ->get(route('blog.show', $post->slug))
        ->assertNotFound();
});
