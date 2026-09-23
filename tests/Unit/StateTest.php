<?php

namespace Tests\Unit;

use App\Models\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\TestCase;

class StateTest extends TestCase
{
    public function test_country_relationship(): void
    {
        $state = new State;
        $country = $state->country();

        $this->assertInstanceOf(
            BelongsTo::class,
            $country,
        );
    }

    public function test_district_relationship(): void
    {
        $state = new State;
        $district = $state->district();

        $this->assertInstanceOf(
            HasMany::class,
            $district,
        );
    }
}
