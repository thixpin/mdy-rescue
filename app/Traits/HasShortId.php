<?php

namespace App\Traits;

use Hidehalo\Nanoid\Client;

trait HasShortId
{
    protected static function bootHasShortId()
    {
        static::creating(function ($model) {
            if (!$model->short_id) {
                $model->short_id = static::generateShortId();
            }
        });
    }

    protected static function generateShortId()
    {
        $client = new Client();
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        do {
            // Generate a 10-character ID using only alphanumeric characters
            $shortId = $client->formattedId($alphabet, 10);
        } while (static::where('short_id', $shortId)->exists());

        return $shortId;
    }

    public function getRouteKeyName()
    {
        return 'short_id';
    }
} 