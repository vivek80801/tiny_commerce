<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;

use function App\Helpers\getGuestTokenKey;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $token;

    /**
     * @var CartService
     */
    private $cartService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->faker()->password(5, 20);

        $this->user = User::factory()->create([
            'password' => $this->password,
        ]);

        $this->token = 'something';

        $this->cartService = app(CartService::class);

        Category::factory()->create();
        $product = Product::factory()->create();

        $this->cartService->addToCart(
            $product,
            null,
            $this->token,
        );

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );
    }

    public function test_register_should_render(): void
    {
        $response = $this->get(
            route('register')
        );

        $response->assertViewIs('auth.register');
        $response->assertStatus(200);
    }

    public function test_login_should_render(): void
    {
        $response = $this->get(
            route('login')
        );

        $response->assertViewIs('auth.login');
        $response->assertStatus(200);
    }

    public function test_register_post_name_length_is_small(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => 'hi',
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password(5, 20),
            ]
        );

        $response->assertSessionHasErrors('name');

        $response->assertStatus(302);
    }

    public function test_register_post_name_length_is_big(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => 'abcdefghiklmnopqurstuvwxyz',
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password(5, 20),
            ]
        );

        $response->assertSessionHasErrors('name');

        $response->assertStatus(302);
    }

    public function test_register_post_email_should_be_email_type(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => $this->faker()->name(),
                'email' => 'thisthat',
                'password' => $this->faker()->password(5, 20),
            ]
        );

        $response->assertSessionHasErrors('email');

        $response->assertStatus(302);
    }

    public function test_register_post_email_should_be_unique(): void
    {
        $user = User::factory()->create();
        $response = $this->post(
            route('register'),
            [
                'name' => $this->faker()->name(),
                'email' => $user->email,
                'password' => $this->faker()->password(5, 20),
            ]
        );

        $response->assertSessionHasErrors('email');

        $response->assertStatus(302);
    }

    public function test_register_post_password_is_small(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => $this->faker()->name(),
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password(1, 5),
            ]
        );

        $response->assertSessionHasErrors('password');

        $response->assertStatus(302);
    }

    public function test_register_post_password_is_big(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => $this->faker()->name(),
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password(20, 30),
            ]
        );

        $response->assertSessionHasErrors('password');

        $response->assertStatus(302);
    }

    public function test_register_post_password_is_not_confirmed(): void
    {
        $response = $this->post(
            route('register'),
            [
                'name' => $this->faker()->name(),
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password(5, 20),
                'password_confirmation' => '',
            ]
        );

        $response->assertSessionHasErrors('password');

        $response->assertStatus(302);
    }

    public function test_register_successful(): void
    {
        $password = $this->faker()->password(5, 20);
        $name = $this->faker()->name();
        $email = $this->faker()->email();

        $response = $this->post(
            route('register'),
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
            ]
        );

        $response->assertSessionHasNoErrors();

        $user = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('dashboard'));
        $response->assertStatus(302);
    }

    public function test_login_post_route_login_email_should_not_exists(): void
    {
        $response = $this->post(
            route('login'),
            [
                'email' => $this->faker()->email(),
                'password' => $this->faker()->password,
            ]
        );

        $response->assertSessionHasErrors('auth');

        $response->assertStatus(302);
    }

    public function test_login_with_wrong_email(): void
    {
        $response = $this->post(
            route('login'),
            [
                'email' => $this->faker()->email(),
                'password' => $this->password,
            ]
        );

        $response->assertSessionHasErrors('auth');

        $response->assertStatus(302);
    }

    public function test_login_with_wrong_password(): void
    {
        $response = $this->post(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->faker()->password(5, 20),
            ]
        );

        $response->assertSessionHasErrors('auth');

        $response->assertStatus(302);
    }

    public function test_login_with_correct_credentials(): void
    {
        $response = $this->post(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->password,
            ]
        );

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($this->user);

        $response->assertRedirect(route('dashboard'));
        $response->assertStatus(302);
    }

    public function test_login_if_is_admin(): void
    {
        $this->user->is_admin = true;
        $this->user->save();

        $response = $this->post(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->password,
            ]
        );

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(
            route('filament.admin.auth.login')
        );

        $response->assertStatus(302);
    }

    public function test_dashboard_should_work(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_logout(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertStatus(302);
    }

    public function test_register_with_cart(): void
    {
        $password = $this->faker()->password(5, 20);
        $name = $this->faker()->name();
        $email = $this->faker()->email();

        $response = $this->withCookie(
            getGuestTokenKey(),
            $this->token,
        )
            ->post(
                route('register'),
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $password,
                ]
            );

        $user = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($user);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertStatus(302);
    }

    public function test_login_user_with_cart_and_guest_cart(): void
    {
        $response = $this->withCookie(
            getGuestTokenKey(),
            $this->token,
        )
            ->post(
                route('login'),
                [
                    'email' => $this->user->email,
                    'password' => $this->password,
                ]
            );

        $this->assertAuthenticatedAs($this->user);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertStatus(302);
    }
}
