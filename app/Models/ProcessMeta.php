<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessMeta extends Model
{
    use HasFactory;
    protected $table = 'process_meta';
    protected $guarded = [];

    // Automatically generate UUID when creating a new item
    public static function boot()
    {
        parent::boot();
        /*
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });


        static::creating(function ($model) {
            $model->process_id = self::generateUuid();
        });
        */
    }
    public static function generateUuid()
    {
        do {
            $prefix = 'PROCESS';
            $length = 16;
            $numericUUID = str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $uniqueUUID = $prefix . $numericUUID;
            $exists = ProcessMeta::where('process_id', $uniqueUUID)->exists();
        } while ($exists);
        return $uniqueUUID;
    }


    // If using polymorphic relationships
    public function model()
    {
        return $this->morphTo();
    }
}
