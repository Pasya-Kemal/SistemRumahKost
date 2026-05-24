<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id'     => Booking::factory(),
            'amount'         => $this->faker->numberBetween(10, 50) * 100000, 
            'payment_date'   => $this->faker->date(),
            'payment_record' => [
                            {
                                "transaction_reference": "TRX-1716550533-BKG-88",
                                "payment_channel": "bank_transfer",
                                "snapshots": {
                                    "tenant": {
                                    "name": "Teguh Ilham",
                                    "email": "teguh@example.com"
                                    },
                                    "property": {
                                    "kost_name": "Kost Cendana",
                                    "room_name": "Kamar VIP 1",
                                    "price_per_month": 1500000
                                    }
                                },
                                "financial_recap": {
                                    "duration_months": 6,
                                    "base_amount": 9000000,
                                    "admin_fee": 0,
                                    "total_paid": 9000000
                                },
                                "period": {
                                    "start_date": "2026-06-01",
                                    "end_date": "2026-12-01"
                                },
                                "timestamp": "2026-05-24T18:35:33+07:00"
                            }
            ],
            'status'         => $this->faker->randomElement(['paid', 'unpaid']),
        ];
    }
}