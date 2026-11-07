<?php

use App\Livewire\Blog\Comments;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('visitors can see comments section on published post', function () {
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk()
        ->assertSee('Diskusi & Komentar', false)
        ->assertSee('Belum ada diskusi pada artikel ini.');
});

test('guest visitor can post a comment successfully', function () {
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    Livewire::test(Comments::class, ['post' => $post])
        ->set('authorName', 'Budi DevOps')
        ->set('authorEmail', 'budi@devops.test')
        ->set('content', 'Artikel Kubernetes ini sangat membantu, terima kasih!')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('content', '')
        ->assertSee('Komentar Anda berhasil dikirim.')
        ->assertSee('Budi DevOps')
        ->assertSee('Artikel Kubernetes ini sangat membantu, terima kasih!');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => null,
        'author_name' => 'Budi DevOps',
        'author_email' => 'budi@devops.test',
        'content' => 'Artikel Kubernetes ini sangat membantu, terima kasih!',
    ]);
});

test('guest comment validation fails when required fields are missing or invalid', function () {
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    Livewire::test(Comments::class, ['post' => $post])
        ->set('authorName', '')
        ->set('authorEmail', 'invalid-email')
        ->set('content', 'ab')
        ->call('save')
        ->assertHasErrors(['authorName', 'authorEmail', 'content']);

    $this->assertDatabaseCount('comments', 0);
});

test('authenticated user can post comment using their account info', function () {
    $user = User::factory()->create([
        'name' => 'Siti SRE',
        'email' => 'siti@sre.test',
    ]);

    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->actingAs($user);

    Livewire::test(Comments::class, ['post' => $post])
        ->set('content', 'Mantap sekali arsitektur CI/CD ini.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('content', '')
        ->assertSee('Siti SRE')
        ->assertSee('Mantap sekali arsitektur CI/CD ini.');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'author_name' => 'Siti SRE',
        'author_email' => 'siti@sre.test',
        'content' => 'Mantap sekali arsitektur CI/CD ini.',
    ]);
});

test('comment author can delete their own comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $comment = Comment::factory()->forUser($user)->create([
        'post_id' => $post->id,
        'content' => 'Komentar yang akan dihapus sendiri.',
    ]);

    $this->actingAs($user);

    Livewire::test(Comments::class, ['post' => $post])
        ->call('delete', $comment->id)
        ->assertSee('Komentar berhasil dihapus.');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('admin can delete any comment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'content' => 'Spam comment to be removed by admin.',
    ]);

    $this->actingAs($admin);

    Livewire::test(Comments::class, ['post' => $post])
        ->call('delete', $comment->id)
        ->assertSee('Komentar berhasil dihapus.');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('post author writer can delete comments on their own article', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $post = Post::factory()->create([
        'user_id' => $writer->id,
        'status' => 'published',
        'published_at' => now(),
    ]);

    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'content' => 'Feedback to be moderated by author.',
    ]);

    $this->actingAs($writer);

    Livewire::test(Comments::class, ['post' => $post])
        ->call('delete', $comment->id)
        ->assertSee('Komentar berhasil dihapus.');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('unauthorized user cannot delete someone elses comment', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create(['role' => 'user']);
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $comment = Comment::factory()->forUser($author)->create([
        'post_id' => $post->id,
    ]);

    $this->actingAs($otherUser);

    Livewire::test(Comments::class, ['post' => $post])
        ->call('delete', $comment->id)
        ->assertForbidden();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});
