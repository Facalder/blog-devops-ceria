<?php

use App\Livewire\Admin\Categories\Index;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can view categories management page', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'DevOps']);

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertOk()
        ->assertSee('DevOps');
});

test('user can create a category', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('name', 'Cloud Architecture')
        ->set('slug', 'cloud-architecture')
        ->set('description', 'Kategori seputar cloud infra.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'name' => 'Cloud Architecture',
        'slug' => 'cloud-architecture',
    ]);
});

test('user can update a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('edit', $category->id)
        ->set('name', 'Kategori Diperbarui')
        ->set('slug', 'kategori-diperbarui')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Kategori Diperbarui',
    ]);
});

test('user can delete a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $category->id);

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
