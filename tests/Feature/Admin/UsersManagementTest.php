<?php

use App\Livewire\Admin\Users\Index;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can view users management page', function () {
    $user = User::factory()->create(['name' => 'Caldera Writer', 'role' => 'writer']);

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Caldera Writer')
        ->assertSee('Writer');
});

test('user can update another user role', function () {
    $admin = User::factory()->create();
    $targetUser = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin);

    Livewire::test(Index::class)
        ->call('editRole', $targetUser->id)
        ->set('selectedRole', 'writer')
        ->call('updateRole')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'role' => 'writer',
    ]);
});
