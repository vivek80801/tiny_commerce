<?php

use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignIdFor(Country::class, 'country_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignIdFor(State::class, 'state_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignIdFor(District::class, 'district_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function () {});
    }
};
