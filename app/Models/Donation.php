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
        'verified',
        'certificate_url',
    ];

    protected $casts = [
        'donate_date' => 'datetime',
        'verified' => 'boolean',
        'donation_amount' => 'decimal:2',
    ];
}
