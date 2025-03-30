<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'activated_by',
        'activation_status',
        'activation_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activated_by' => 'integer',
        'activation_status' => 'string',
    ];

    public function isActivated()
    {
        return $this->activation_status === 'activated';
    }

    public function isPendingActivation()
    {
        return $this->activation_status === 'pending';
    }

    public function activator()
    {
        return $this->belongsTo(Admin::class, 'activated_by');
    }
}
