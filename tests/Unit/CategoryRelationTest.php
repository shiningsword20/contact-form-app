<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリに紐づく複数のお問い合わせが正しく取得できる(): void
    {
        // Arrange
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create(['category_id' => $category->id]);

        // Act
        $contacts = $category->contacts;

        // Assert
        $this->assertCount(3, $contacts);
    }
}
