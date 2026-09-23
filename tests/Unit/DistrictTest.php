<?php

namespace Tests\Unit;

use App\Models\District;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\TestCase;

class DistrictTest extends TestCase
{
    public function test_state_relationship(): void
    {
        $district = new District;
        $state = $district->state();

        $this->assertInstanceOf(
            BelongsTo::class,
            $state
        );
    }
}
