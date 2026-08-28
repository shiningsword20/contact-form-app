<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証されたユーザーのみ管理画面にアクセスできる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 未認証ユーザーはログイン画面にリダイレクトされる(): void
    {
        // Act
        $response = $this->get('/admin');

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 管理画面で問い合わせが7件ごとにページネーションされる(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        Contact::factory()->count(10)->create(['category_id' => $category->id]);

        // Act
        $response = $this->actingAs($user)->get('/admin');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 7;
        });
    }

    /** @test */
    public function キーワードフィルタで一致する問い合わせを取得する(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $matchContact = Contact::factory()->create([
            'first_name' => '杉林',
            'category_id' => $category->id]);
        $unmatchContact = Contact::factory()->create([
            'first_name' => '高林',
            'category_id' => $category->id]);

        // Act
        $response = $this->actingAs($user)->get('/admin?keyword=杉林');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) use ($matchContact, $unmatchContact) {
            return $contacts->contains($matchContact) && ! $contacts->contains($unmatchContact);
        });
    }

    /** @test */
    public function 性別フィルタで一致する問い合わせを取得する(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $matchContact = Contact::factory()->create([
            'gender' => 1,
            'category_id' => $category->id,
        ]);
        $unmatchContact = Contact::factory()->create([
            'gender' => 2,
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?gender=1');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) use ($matchContact, $unmatchContact) {
            return $contacts->contains($matchContact) && ! $contacts->contains($unmatchContact);
        });
    }

    /** @test */
    public function カテゴリーフィルタで一致する問い合わせを取得する(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $matchContact = Contact::factory()->create([
            'category_id' => $category1->id,
        ]);
        $unmatchContact = Contact::factory()->create([
            'category_id' => $category2->id,
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?category_id=1');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) use ($matchContact, $unmatchContact) {
            return $contacts->contains($matchContact) && ! $contacts->contains($unmatchContact);
        });
    }

    /** @test */
    public function 日付フィルタで一致する問い合わせを取得する(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $matchContact = Contact::factory()->create([
            'created_at' => '2026-08-28 10:00:00',
            'category_id' => $category->id,
        ]);
        $unmatchContact = Contact::factory()->create([
            'created_at' => '2026-08-31 10:00:00',
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?date=2026-08-28');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) use ($matchContact, $unmatchContact) {
            return $contacts->contains($matchContact) && ! $contacts->contains($unmatchContact);
        });
    }

    /** @test */
    public function お問い合わせ詳細が表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
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

        // Act
        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee('杉林');
        $response->assertSee('由樹');
        $response->assertSee('男性');
        $response->assertSee('test123@example.com');
        $response->assertSee('02012345678');
        $response->assertSee('愛知県名古屋市');
        $response->assertSee('バステール101');
        $response->assertSee($category->content);
        $response->assertSee('商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。');
    }

    /** @test */
    public function お問い合わせ内容を削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // Act
        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
