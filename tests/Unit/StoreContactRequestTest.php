<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 有効な問い合わせ内容はバリデーションを通過する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new StoreContactRequest;
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
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な電話番号は拒否される(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $request = new StoreContactRequest;
        $data = [
            'first_name' => '杉林',
            'last_name' => '由樹',
            'gender' => 1,
            'email' => 'test123@example.com',
            'tel' => '02012345678a',
            'address' => '愛知県名古屋市',
            'category_id' => $category->id,
            'detail' => '商品の配送状況について教えてください。発送はいつ頃になりますでしょうか。',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
    }
}
