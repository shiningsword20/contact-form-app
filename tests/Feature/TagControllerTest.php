<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーはタグ編集画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        // Act
        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 認証済みユーザーはタグを作成できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = ['name' => '新しいタグ'];

        // Act
        $response = $this->actingAs($user)->post('/admin/tags', $data);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }

    /** @test */
    public function 認証済みユーザーはタグを更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '配送']);
        $data = ['name' => '更新したタグ'];

        // Act
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", $data);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '更新したタグ',
        ]);
    }

    /** @test */
    public function 認証済みユーザーはタグを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '配送']);

        // Act
        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function 未認証ユーザーはタグ編集画面にアクセスするとログインページにリダイレクトされる(): void
    {
        // Arrange
        $tag = Tag::factory()->create();

        // Act
        $response = $this->get("/admin/tags/{$tag->id}/edit");

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはタグを作成しようとするとログインページにリダイレクトされる(): void
    {
        // Arrange
        $data = ['name' => '新しいタグ'];

        // Act
        $response = $this->post('/admin/tags', $data);

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはタグを更新しようとするとログインページにリダイレクトされる(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['name' => '配送']);
        $data = ['name' => '更新したタグ'];

        // Act
        $response = $this->put("/admin/tags/{$tag->id}", $data);

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証ユーザーはタグを削除しようとするとログインページにリダイレクトされる(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['name' => '配送']);

        // Act
        $response = $this->delete("/admin/tags/{$tag->id}");

        // Assert
        $response->assertRedirect('/login');
    }
}
