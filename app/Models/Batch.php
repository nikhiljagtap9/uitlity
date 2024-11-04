<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Automatically generate UUID when creating a new item
    public static function boot()
    {
        parent::boot();
        /*
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
        */

        static::creating(function ($model) {
            $model->uuid = self::generateUuid();
        });
    }

    private static function generateUuid()
    {
        $prefix = 'BATCH';
        $uniqueString = substr(uniqid(mt_rand(), true), 0, 15);
        return $prefix . $uniqueString;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
