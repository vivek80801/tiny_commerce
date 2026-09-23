<?php

namespace Tests\Unit;

use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\TestCase;

class CountryTest extends TestCase
{
    public function test_state_relationship(): void
    {
        $country = new Country;
        $state = $country->state();

        $this->assertInstanceOf(
            HasMany::class,
            $state
        );
    }
}
