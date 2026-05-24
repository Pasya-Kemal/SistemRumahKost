<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'tenant_id'       => User::factory(),
            'room_id'         => Room::factory(),
            'start_date'      => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'duration_months' => $this->faker->numberBetween(1, 12),
            'status'          => $this->faker->randomElement(['pending', 'active', 'completed']),
        ];
    }
}
