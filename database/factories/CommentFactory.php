<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'parent_id' => null,
            'reply_to_id' => null,
            'user_id' => null,
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'content' => fake()->paragraph(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
            'author_name' => $user->name,
            'author_email' => $user->email,
        ]);
    }

    public function reply(Comment $target): static
    {
        $rootId = $target->parent_id ?? $target->id;

        return $this->state(fn () => [
            'post_id' => $target->post_id,
            'parent_id' => $rootId,
            'reply_to_id' => $target->id,
        ]);
    }
}
