<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $ownerRole = Role::firstOrCreate(['role_name' => 'owner']);
        $tenantRole = Role::firstOrCreate(['role_name' => 'tenants']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@kostnest.id'],
            ['role_id' => $adminRole->id, 'name' => 'Administrator', 'password' => Hash::make('admin123')]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@kostnest.id'],
            ['role_id' => $ownerRole->id, 'name' => 'Owner Kost', 'password' => Hash::make('owner123')]
        );

        $tenantUser = User::firstOrCreate(
            ['email' => 'tenant@kostnest.id'],
            ['role_id' => $tenantRole->id, 'name' => 'Budi Wibowo', 'password' => Hash::make('tenant123')]
        );

        $kost = Kost::firstOrCreate(
            ['kost_name' => 'Kost Anggrek'],
            ['owner_id' => $owner->id, 'kost_gender' => 'campur', 'address' => 'Jl. Mawar No. 10, Bandung']
        );

        $room = Room::firstOrCreate(
            ['room_name' => '12B', 'kost_id' => $kost->id],
            ['price' => 1200000, 'description' => 'Kamar nyaman, kamar mandi dalam', 'is_available' => false]
        );

        $tenant = Tenant::firstOrCreate(
            ['user_id' => $tenantUser->id],
            [
                'phone' => '081234567890',
                'gender' => 'male',
                'address' => 'Jl. Melati No. 5, Bandung',
                'emergency_contact' => '081298765432',
                'status' => 'active',
                'joined_at' => now()->toDateString(),
            ]
        );

        $booking = Booking::firstOrCreate(
            ['tenant_id' => $tenantUser->id, 'room_id' => $room->id],
            ['start_date' => now()->toDateString(), 'duration_months' => 6, 'status' => 'active']
        );

        Payment::firstOrCreate(
            ['booking_id' => $booking->id, 'payment_date' => now()->toDateString()],
            [
                'amount' => 1200000,
                'payment_record' => ['method' => 'transfer', 'reference' => 'INV-0001', 'payer' => $tenantUser->name],
                'status' => 'paid',
            ]
        );

        Complaint::firstOrCreate(
            ['tenant_id' => $tenant->id, 'room_id' => $room->id, 'title' => 'AC rusak'],
            [
                'description' => 'AC tidak dingin sejak semalam',
                'status' => 'resolved',
                'submitted_at' => now()->toDateString(),
                'resolved_at' => now()->toDateString(),
            ]
        );
    }
}