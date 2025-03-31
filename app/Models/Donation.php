<?php

namespace App\Models;

use App\Enums\Currency;
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
        'currency',
        'amount_in_text',
        'donate_date',
        'verified',
        'certificate_url',
    ];

    protected $casts = [
        'donate_date' => 'datetime',
        'verified' => 'boolean',
        'currency' => Currency::class,
        'donation_amount' => 'decimal:2',
    ];

    public function getFormattedAmountAttribute()
    {
        return $this->currency->format($this->donation_amount);
    }
}
