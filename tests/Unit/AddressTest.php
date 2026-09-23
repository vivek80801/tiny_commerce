<?php

namespace Tests\Unit;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\TestCase;

class AddressTest extends TestCase
{
    public function test_country_relationship(): void
    {
        $address = new Address;
        $country = $address->country();

        $this->assertInstanceOf(
            BelongsTo::class, $country
        );
    }

    public function test_state_relationship(): void
    {
        $address = new Address;
        $state = $address->state();

        $this->assertInstanceOf(
            BelongsTo::class, $state
        );
    }

    public function test_district_relationship(): void
    {
        $address = new Address;
        $district = $address->district();

        $this->assertInstanceOf(
            BelongsTo::class, $district
        );
    }
}
