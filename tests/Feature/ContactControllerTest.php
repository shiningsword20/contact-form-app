<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせフォーム入力ページが正常に表示される(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
        $response->assertSee($category->content);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function サンクスページが正常に表示される(): void
    {
        // Act
        $response = $this->get('/thanks');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function お問い合わせフォーム確認ページ・入力内容が表示される(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678',
            'address' => '愛知県名古屋市',
            'building' => 'バステール101',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('杉林');
        $response->assertSee('由樹');
        $response->assertSee('男性');
        $response->assertSee('test123@example.com');
        $response->assertSee('02012345678');
        $response->assertSee('愛知県名古屋市');
        $response->assertSee('バステール101');
        $response->assertSee($category->content);
        $response->assertSee('商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。');
        $response->assertSee($tag->name);
    }

    /** @test */
    public function お問い合わせ内容の確認にてバリデーションエラー時はリダイレクトされる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678a',
            'address' => '愛知県名古屋市',
            'building' => 'バステール101',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertRedirect('/');
        $response->assertSessionHasErrors('tel');
    }

    /** @test */
    public function お問い合わせ内容がcontact・contact_tagテーブルに保管され、サンクスページに遷移する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678',
            'address' => '愛知県名古屋市',
            'building' => 'バステール101',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts', $data);

        // Assert
        $response->assertRedirect('/thanks');
        $this->assertDatabaseHas('contacts', [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678',
            'address' => '愛知県名古屋市',
            'building' => 'バステール101',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
        ]);
        $this->assertDatabaseHas('contact_tag', [
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function お問い合わせ内容の送信にてバリデーションエラー時はリダイレクトされる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678a',
            'address' => '愛知県名古屋市',
            'building' => 'バステール101',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts', $data);

        // Assert
        $response->assertRedirect('/');
        $response->assertSessionHasErrors('tel');
    }
}
