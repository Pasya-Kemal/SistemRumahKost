<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Kost;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'kost_id'      => Kost::factory(),
            'room_name'    => 'Kamar ' . strtoupper($this->faker->bothify('?#')),
            'price'        => $this->faker->numberBetween(5, 30) * 100000, 
            'description'  => $this->faker->paragraph(),
            'is_available' => $this->faker->boolean(70), 
        ];
    }
}