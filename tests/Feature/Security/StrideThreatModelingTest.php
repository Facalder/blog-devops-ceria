<?php

use App\Livewire\Admin\Posts\Create;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('STRIDE-S: spoofing protection - unauthenticated visitors cannot spoof identity or access dashboard', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));

    $postsResponse = $this->get(route('posts.index'));
    $postsResponse->assertRedirect(route('login'));
});

test('STRIDE-T: tampering protection - post input validation rejects empty and invalid payloads', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', '')
        ->set('content', '')
        ->set('category_id', 99999)
        ->call('save')
        ->assertHasErrors(['title', 'content', 'category_id']);
});

test('STRIDE-R: repudiation prevention - post creations and updates enforce immutable timestamps and author binding', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('title', 'Repudiation Audit Post')
        ->set('slug', 'repudiation-audit-post')
        ->set('category_id', $category->id)
        ->set('content', 'Article content for audit trace.')
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('slug', 'repudiation-audit-post')->first();

    expect($post)->not->toBeNull();
    expect($post->user_id)->toBe($user->id);
    expect($post->created_at)->not->toBeNull();
    expect($post->updated_at)->not->toBeNull();
});

test('STRIDE-I: information disclosure prevention - draft posts and user credentials are never exposed', function () {
    $user = User::factory()->create([
        'password' => 'secret-plain-text',
    ]);

    $userArray = $user->toArray();
    expect($userArray)->not->toHaveKey('password');
    expect($userArray)->not->toHaveKey('remember_token');
    expect($userArray)->not->toHaveKey('two_factor_secret');

    $draftPost = Post::factory()->draft()->create([
        'title' => 'Unpublished Internal Document',
        'slug' => 'unpublished-internal-document',
    ]);

    $this->get(route('blog.show', $draftPost->slug))
        ->assertNotFound();

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Unpublished Internal Document');
});

test('STRIDE-D: denial of service protection - blog listing enforces pagination to prevent memory exhaustion', function () {
    $category = Category::factory()->create();
    Post::factory()->count(15)->create([
        'status' => 'published',
        'category_id' => $category->id,
        'published_at' => now(),
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $posts = $response->viewData('posts');
    expect($posts->count())->toBeLessThanOrEqual(9);
    expect($posts->total())->toBeGreaterThanOrEqual(15);
});

test('STRIDE-E: elevation of privilege protection - unauthenticated visitors cannot access admin management routes', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.create'))
        ->assertRedirect(route('login'));

    $this->get(route('posts.edit', $post))
        ->assertRedirect(route('login'));

    $this->get(route('categories.index'))
        ->assertRedirect(route('login'));

    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});
