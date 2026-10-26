<?php

use App\Models\Category;
use App\Models\Post;

test('visitors can visit homepage global view and see published posts', function () {
    $category = Category::factory()->create(['name' => 'Kubernetes']);
    $post = Post::factory()->create([
        'title' => 'Deploying Apps on K8s',
        'status' => 'published',
        'category_id' => $category->id,
        'published_at' => now(),
    ]);

    $draft = Post::factory()->draft()->create([
        'title' => 'Draft Secret Article',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Deploying Apps on K8s')
        ->assertSee('Kubernetes')
        ->assertDontSee('Draft Secret Article');
});

test('visitors can read published article single page', function () {
    $post = Post::factory()->create([
        'title' => 'Deep Dive GitOps',
        'slug' => 'deep-dive-gitops',
        'content' => '## GitOps Workflow\n\nGitOps is awesome.',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk()
        ->assertSee('Deep Dive GitOps')
        ->assertSee('GitOps is awesome');
});

test('visitors can search published articles by title or category on homepage', function () {
    $category = Category::factory()->create(['name' => 'Container Orchestration']);

    Post::factory()->create([
        'title' => 'Tutorial Docker Container',
        'status' => 'published',
        'published_at' => now(),
    ]);

    Post::factory()->create([
        'title' => 'Deploying Apps on K8s',
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now(),
    ]);

    Post::factory()->create([
        'title' => 'Belajar Terraform Cloud',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('home', ['search' => 'Container']));

    $response->assertOk()
        ->assertSee('Tutorial Docker Container')
        ->assertSee('Deploying Apps on K8s')
        ->assertDontSee('Belajar Terraform Cloud');
});

test('draft articles return 404 for public visitors', function () {
    $draft = Post::factory()->draft()->create([
        'slug' => 'secret-draft',
    ]);

    $response = $this->get(route('blog.show', $draft->slug));

    $response->assertNotFound();
});
