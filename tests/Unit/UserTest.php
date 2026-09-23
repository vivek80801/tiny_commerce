<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\TestCase;

class UserTest extends TestCase
{
    public function test_cart_relationship(): void
    {
        $user = new User;
        $cart = $user->cart();

        $this->assertInstanceOf(
            HasMany::class,
            $cart
        );
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $user = new User;
        $user->is_admin = true;

        $panel = $this->createMock(Panel::class);
        $panel->method('getId')->willReturn('admin');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_user_who_is_not_admin_should_not_access_admin_panel():void
    {
        $user = new User;
        $user->is_admin = false;

        $panel = $this->createMock(Panel::class);
        $panel->method('getId')->willReturn('admin');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_if_not_admin_panel(): void
    {
        $user = new User;
        $user->is_admin = false;

        $panel = $this->createMock(Panel::class);
        $panel->method('getId')->willReturn('user_panel');

        $this->assertFalse($user->canAccessPanel($panel));
    }

}
