<?php

namespace Tests\Feature\Filament\Resources\Categories;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Override;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_render_page(): void
    {
        $response = $this->get(
            CategoryResource::getUrl(
                'index'
            )
        );

        $response->assertStatus(302);
    }

    public function test_list_category(): void
    {
        $categories = Category::factory(6)->create();

        Livewire::test(ListCategories::class)
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_create_category(): void
    {
        $this->assertEquals(0, Category::count());
        $file = UploadedFile::fake()
            ->image(
                'category.jpg',
                800,
                900
            );

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => $this->faker()->name(),
                'image.filename' => $file,
            ])
            ->call('create')
            ->assertHasNoErrors();
    }
}
