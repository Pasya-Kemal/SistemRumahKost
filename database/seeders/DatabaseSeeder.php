<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Kost;
use App\Models\Photo;
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
        // ---------- Roles ----------
        $adminRole  = Role::firstOrCreate(['role_name' => 'admin']);
        $ownerRole  = Role::firstOrCreate(['role_name' => 'owner']);
        $tenantRole = Role::firstOrCreate(['role_name' => 'tenants']);

        // ---------- Core users (used for login previews) ----------
        $admin = User::firstOrCreate(
            ['email' => 'admin@kostnest.id'],
            ['role_id' => $adminRole->id, 'name' => 'Super Admin', 'password' => Hash::make('admin123')]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@kostnest.id'],
            ['role_id' => $ownerRole->id, 'name' => 'Pak Hartono', 'password' => Hash::make('owner123')]
        );

        $tenantUser = User::firstOrCreate(
            ['email' => 'tenant@kostnest.id'],
            ['role_id' => $tenantRole->id, 'name' => 'Budi Wibowo', 'password' => Hash::make('tenant123')]
        );

        // A second owner so the admin view shows cross-owner data.
        $owner2 = User::firstOrCreate(
            ['email' => 'owner2@kostnest.id'],
            ['role_id' => $ownerRole->id, 'name' => 'Bu Sari', 'password' => Hash::make('owner123')]
        );

        // An Admin created by Pak Hartono — scoped to his kosts only.
        User::firstOrCreate(
            ['email' => 'admin-hartono@kostnest.id'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Rani Staff Hartono',
                'password' => Hash::make('admin123'),
            ]
        );

        // ---------- Kosts (owner = Pak Hartono has 3, Bu Sari has 1) ----------
        $kostsData = [
            ['kost_name' => 'Kost Anggrek',     'owner_id' => $owner->id,  'kost_gender' => 'Campuran', 'address' => 'Jl. Mawar No. 10, Bandung'],
            ['kost_name' => 'Kost Cendrawasih', 'owner_id' => $owner->id,  'kost_gender' => 'Pria',     'address' => 'Jl. Sudirman No. 22, Jakarta Selatan'],
            ['kost_name' => 'Kost Melati Putih','owner_id' => $owner->id,  'kost_gender' => 'Wanita',   'address' => 'Jl. Diponegoro No. 5, Yogyakarta'],
            ['kost_name' => 'Kost Bougenville', 'owner_id' => $owner2->id, 'kost_gender' => 'Wanita',   'address' => 'Jl. Asia Afrika No. 88, Bandung'],
            ['kost_name' => 'Kost Mawar Biru',  'owner_id' => $owner2->id, 'kost_gender' => 'Campuran', 'address' => 'Jl. Pemuda No. 7, Surabaya'],
        ];

        $kosts = collect($kostsData)->map(function ($d) {
            return Kost::firstOrCreate(['kost_name' => $d['kost_name']], $d);
        });

        // ---------- Rooms (varied prices, mostly available) ----------
        $roomsBlueprint = [
            // Kost Anggrek
            ['kost' => 'Kost Anggrek',     'room_name' => 'A-101', 'price' => 1200000, 'description' => 'Kamar standar dengan kasur queen, lemari, dan meja kerja.', 'is_available' => true],
            ['kost' => 'Kost Anggrek',     'room_name' => 'A-102', 'price' => 1400000, 'description' => 'Kamar mandi dalam, AC, WiFi cepat.',                       'is_available' => true],
            ['kost' => 'Kost Anggrek',     'room_name' => 'A-103', 'price' => 1100000, 'description' => 'Cocok untuk pelajar, dekat kampus.',                       'is_available' => false],
            ['kost' => 'Kost Anggrek',     'room_name' => 'A-104', 'price' => 1300000, 'description' => 'Kamar sudut dengan jendela besar dan sirkulasi udara baik.',  'is_available' => true],
            // Kost Cendrawasih
            ['kost' => 'Kost Cendrawasih', 'room_name' => 'C-201', 'price' => 1500000, 'description' => 'Khusus pria, eksklusif lantai 2, kamar luas.',             'is_available' => true],
            ['kost' => 'Kost Cendrawasih', 'room_name' => 'C-202', 'price' => 1350000, 'description' => 'Pria, kamar dengan balkon kecil.',                         'is_available' => true],
            ['kost' => 'Kost Cendrawasih', 'room_name' => 'C-203', 'price' => 1450000, 'description' => 'Kamar dekat tangga, akses cepat ke area bersama.',               'is_available' => true],
            // Kost Melati Putih
            ['kost' => 'Kost Melati Putih','room_name' => 'M-301', 'price' => 1600000, 'description' => 'Khusus wanita, dapur bersama, area kerja nyaman.',         'is_available' => true],
            ['kost' => 'Kost Melati Putih','room_name' => 'M-302', 'price' => 1700000, 'description' => 'Kamar premium, ranjang king, WiFi 100Mbps.',               'is_available' => true],
            // Kost Bougenville (owner2)
            ['kost' => 'Kost Bougenville', 'room_name' => 'B-401', 'price' => 1800000, 'description' => 'Wanita, dekat pusat kota, fasilitas lengkap.',             'is_available' => true],
            ['kost' => 'Kost Bougenville', 'room_name' => 'B-402', 'price' => 1750000, 'description' => 'Wanita, kamar tenang menghadap taman.',                    'is_available' => true],
            // Kost Mawar Biru (owner2, Surabaya)
            ['kost' => 'Kost Mawar Biru',  'room_name' => 'MB-501','price' => 900000,  'description' => 'Kamar ekonomis Surabaya, dekat stasiun.',                  'is_available' => true],
            ['kost' => 'Kost Mawar Biru',  'room_name' => 'MB-502','price' => 1050000, 'description' => 'Kamar dengan kipas angin, lingkungan tenang.',             'is_available' => true],
            ['kost' => 'Kost Mawar Biru',  'room_name' => 'MB-503','price' => 1150000, 'description' => 'Kamar pojok dengan meja belajar dan lemari pakaian.',       'is_available' => true],
        ];

        $rooms = collect();
        foreach ($roomsBlueprint as $rb) {
            $kost = $kosts->firstWhere('kost_name', $rb['kost']);
            $rooms->push(Room::firstOrCreate(
                ['room_name' => $rb['room_name'], 'kost_id' => $kost->id],
                [
                    'price'        => $rb['price'],
                    'description'  => $rb['description'],
                    'is_available' => $rb['is_available'],
                ]
            ));
        }

        $roomPhotos = [
            'A-101' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'A-102' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'A-103' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'A-104' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'C-201' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'C-202' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'C-203' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'M-301' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'M-302' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'B-401' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'B-402' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'MB-501' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'MB-502' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
            'MB-503' => ['room_photos/131-3-sewakostcom-768x512.jpeg'],
        ];

        foreach ($rooms as $room) {
            $paths = $roomPhotos[$room->room_name] ?? ['room_photos/131-3-sewakostcom-768x512.jpeg'];

            Photo::where('room_id', $room->id)->delete();

            foreach ($paths as $i => $path) {
                Photo::create([
                    'room_id' => $room->id,
                    'image_path' => $path,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        // ---------- Tenant profile ----------
        $tenant = Tenant::firstOrCreate(
            ['user_id' => $tenantUser->id],
            [
                'phone'             => '081234567890',
                'gender'            => 'male',
                'address'           => 'Jl. Melati No. 5, Bandung',
                'emergency_contact' => '081298765432',
                'status'            => 'active',
                'joined_at'         => now()->toDateString(),
            ]
        );

        // ---------- A booked room for the tenant (A-103, marked unavailable) ----------
        $bookedRoom = $rooms->firstWhere('room_name', 'A-103');

        $booking = Booking::firstOrCreate(
            ['tenant_id' => $tenantUser->id, 'room_id' => $bookedRoom->id],
            ['start_date' => now()->subMonths(1)->toDateString(), 'duration_months' => 6, 'status' => 'active']
        );

        // ---------- Payments for the booking (last 2 months) ----------
        Payment::firstOrCreate(
            ['booking_id' => $booking->id, 'payment_date' => now()->subMonth()->toDateString()],
            [
                'amount'         => 1100000,
                'payment_record' => ['method' => 'transfer', 'reference' => 'INV-0001', 'payer' => $tenantUser->name],
                'status'         => 'paid',
            ]
        );
        Payment::firstOrCreate(
            ['booking_id' => $booking->id, 'payment_date' => now()->toDateString()],
            [
                'amount'         => 1100000,
                'payment_record' => ['method' => 'transfer', 'reference' => 'INV-0002', 'payer' => $tenantUser->name],
                'status'         => 'paid',
            ]
        );

        // ---------- Complaint sample ----------
        Complaint::firstOrCreate(
            ['tenant_id' => $tenant->id, 'room_id' => $bookedRoom->id, 'title' => 'AC kurang dingin'],
            [
                'description'  => 'AC tidak dingin sejak semalam, mohon dicek teknisi.',
                'status'       => 'resolved',
                'submitted_at' => now()->subDays(3)->toDateString(),
                'resolved_at'  => now()->toDateString(),
            ]
        );
    }
}
