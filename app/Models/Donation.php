<?php

namespace App\Models;

use App\Traits\HasShortId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory, HasShortId;

    protected $fillable = [
        'name',
        'description',
        'donation_amount',
        'amount_in_text',
        'donate_date',
        'certificate_url',
        'verified',
        'donation_target_id',
    ];

    protected $casts = [
        'donation_amount' => 'decimal:2',
        'donate_date' => 'date',
        'verified' => 'boolean',
    ];

    public function donationTarget()
    {
        return $this->belongsTo(DonationTarget::class);
    }
}
