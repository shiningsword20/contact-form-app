<?php

namespace Tests\Unit;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 自身のタグ名登録はバリデーションを通過する(): void
    {
        // Arrange
        $tag = Tag::factory()->create(['name' => 'お金']);
        $rule = [
            'name' => [
                'required', 'string', 'max:50',
                'unique:tags,name,'.$tag->id,
            ],
        ];

        $data = ['name' => 'お金'];

        // Act
        $validator = Validator::make($data, $rule);

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function タグ名が重複している場合は拒否される(): void
    {
        // Arrange
        $tag1 = Tag::factory()->create(['name' => 'お金']);
        $tag2 = Tag::factory()->create(['name' => '配送']);
        $rule = [
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:tags,name,'.$tag1->id,
            ],
        ];
        $data = [
            'name' => '配送',
        ];

        // Act
        $validator = Validator::make($data, $rule);

        // Assert
        $this->assertFalse($validator->passes());
    }
}
