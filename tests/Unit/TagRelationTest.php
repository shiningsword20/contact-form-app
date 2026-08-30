<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagRelationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグは複数のお問い合わせに紐づいている(): void
    {
        // Arrange
        Category::factory()->create();
        $tag = Tag::factory()->create(['name' => '配送']);
        $contact1 = Contact::factory()->create();
        $contact2 = Contact::factory()->create();
        $contact3 = Contact::factory()->create();

        // Act
        $contact1->tags()->attach($tag->id);
        $contact2->tags()->attach($tag->id);
        $contact3->tags()->attach($tag->id);
        // Assert
        $this->assertCount(3, $tag->contacts);
    }
}
