<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SessionFactory extends Factory
{
    /**
     * Define the model's default state.
     */

    protected $model = Session::class;
    
    public function definition(): array
    {
        return [
            'id_station'   => random_int(1, 5),
            'start_at'     => $this->faker->dateTimeThisMonth(),
            'initial_mass' => random_int(1000, 5000),
        ];
    }
}
