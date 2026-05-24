<?php

namespace Database\Factories;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KostFactory extends Factory
{
    protected $model = Kost::class;

    public function definition(): array
    {
        return [
            'owner_id'    => User::factory(), 
            'kost_name'   => 'Kost ' . $this->faker->company(),
            'kost_gender' => $this->faker->randomElement(['Pria', 'Wanita', 'Campuran']),
            'address'     => $this->faker->address(),
        ];
    }
}