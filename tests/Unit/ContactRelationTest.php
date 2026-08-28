<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせは特定のカテゴリに属する(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // Assert
        $this->assertEquals($category->id, $contact->category->id);
    }

    /** @test */
    public function お問い合わせは複数のタグと同期できる(): void
    {
        // Arrange
        Category::factory()->create();
        $tag1 = Tag::factory()->create(['name' => '配送']);
        $tag2 = Tag::factory()->create(['name' => '返品']);
        $tag3 = Tag::factory()->create(['name' => '交換']);
        $contact = Contact::factory()->create();

        // Act
        $contact->tags()->attach([$tag1->id, $tag2->id]);
        $contact->tags()->sync([$tag1->id, $tag3->id]);

        // Assert
        $this->assertTrue($contact->tags->contains($tag1));
        $this->assertFalse($contact->tags->contains($tag2));
        $this->assertTrue($contact->tags->contains($tag3));
    }
}
