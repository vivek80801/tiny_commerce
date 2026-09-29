<?php

namespace Tests\Feature\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Override;

class UserResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    private $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create();

        $this->user->is_admin = true;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_can_render_page(): void
    {
        $response = $this->get(
            UserResource::getUrl(
                'index'
            )
        );

        $response->assertSuccessful();
    }

    public function test_list_user(): void
    {
        Livewire::test(
            ListUsers::class
        )->assertCanSeeTableRecords([$this->user]);
    }

    public function test_can_create_user(): void
    {
        $this->assertEquals(1, User::count());

        Livewire::test(
            CreateUser::class
        )->fillForm([
            'name' => $this->faker()->name(),
            'email' => $this->faker()->safeEmail(),
            'password' => '123456',
            'password_confirmation' => '123456',
            'is_admin' => false,
        ])
            ->call('create')
            ->assertHasNoErrors();
    }
}
