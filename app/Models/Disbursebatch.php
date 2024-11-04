<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disbursebatch extends Model
{
    use HasFactory;
    use SoftDeletes;
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
        do {
            $prefix = 'BATCH';
            $length = 15;
            $numericUUID = str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $uniqueUUID = $prefix . $numericUUID;
            $exists = Disbursebatch::where('uuid', $uniqueUUID)->exists();
        } while ($exists);
        return $uniqueUUID;
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
