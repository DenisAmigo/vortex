<?php

namespace Tests\Unit;

use App\Livewire\PostComments;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function author_can_soft_delete_their_comment()
    {
        // Arrange
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
        ]);

        // Act
        Livewire::actingAs($author)
            ->test(PostComments::class, ['post' => $post])
            ->call('deleteComment', $comment->id);

        // Assert
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    #[Test]
    public function likes_are_deleted_when_comment_is_soft_deleted()
    {
        // Arrange
        $author = User::factory()->create();
        $liker = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
        ]);

        $comment->likes()->create(['user_id' => $liker->id]);

        // Act
        Livewire::actingAs($author)
            ->test(PostComments::class, ['post' => $post])
            ->call('deleteComment', $comment->id);

        // Assert
        $this->assertEmpty($comment->likes);

        // Проверяем soft delete
        $this->assertSoftDeleted($comment);
    }

    #[Test]
    public function non_author_cannot_delete_comment()
    {
        // Arrange
        $author = User::factory()->create();
        $intruder = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
        ]);

        // Act
        Livewire::actingAs($intruder)
            ->test(PostComments::class, ['post' => $post])
            ->call('deleteComment', $comment->id)
            ->assertForbidden();

        // Assert
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function replies_are_preserved_after_parent_is_soft_deleted()
    {
        // Arrange
        $author = User::factory()->create();
        $replier = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $parent = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
        ]);

        $reply = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $replier->id,
            'parent_id' => $parent->id,
        ]);

        // Act
        Livewire::actingAs($author)
            ->test(PostComments::class, ['post' => $post])
            ->call('deleteComment', $parent->id);

        // Assert
        $this->assertSoftDeleted('comments', ['id' => $parent->id]);
        $this->assertDatabaseHas('comments', [
            'id' => $reply->id,
            'deleted_at' => null, // ответ не удалён
        ]);
    }
}
