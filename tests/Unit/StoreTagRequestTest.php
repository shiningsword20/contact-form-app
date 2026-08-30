<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 有効なタグ登録はバリデーションを通過する(): void
    {
        // Arrange
        $request = new StoreTagRequest;
        $data = [
            'name' => 'お金',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function タグ登録で文字数超過は拒否される(): void
    {
        // Arrange
        $request = new StoreTagRequest;
        $data = [
            'name' => str_repeat('あ', 51),
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function タグ登録でnameの重複は拒否される(): void
    {
        // Arrange
        Tag::factory()->create(['name' => 'お金']);
        $request = new StoreTagRequest;
        $data = [
            'name' => 'お金',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertFalse($validator->passes());
    }
}
