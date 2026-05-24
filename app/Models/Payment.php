<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass Assignment Protection
     */
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_date',
        'payment_record',
        'status',
    ];


    protected $casts = [
        'amount'         => 'integer',
        'payment_date'   => 'date',  
        'payment_record' => 'array',
    ];

    /**
     * Relation: Payment-Booking (one to one)
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    /**
     * Booted method: "Immutable Financial record"
     */
    protected static function booted()
    {
        // Event listener: Before Update Attempt
        static::updating(function ($payment) {
            // isDirty() mengecek apakah kolom tersebut sedang dicoba untuk diubah
            if ($payment->isDirty('amount') || $payment->isDirty('booking_id')) {
                // Hentikan proses secara paksa. Nominal dan tujuan pembayaran HARAM diubah.
                throw new \Exception('Pelanggaran Integritas Finansial: Nominal (amount) dan referensi (booking_id) tidak boleh diubah setelah transaksi dicatat.');
            }
        });

        // Event listener: efore Delete Attempt
        static::deleting(function ($payment) {
            if ($payment->status === 'paid') {
                throw new \Exception('Pelanggaran Integritas Finansial: Riwayat pembayaran yang sudah lunas tidak dapat dihapus. Silakan gunakan mekanisme Refund jika diperlukan.');
            }
        });
    }
}