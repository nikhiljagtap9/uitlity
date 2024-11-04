<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disbursement extends Model
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
            $model->lapp_id = self::generateUuid();
        });
    }
    private static function generateUuid()
    {
        do {
            $prefix = 'APP';
            $length = 16;
            $numericUUID = str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $uniqueUUID = $prefix . $numericUUID;
            $exists = Disbursement::where('lapp_id', $uniqueUUID)->exists();
        } while ($exists);
        return $uniqueUUID;
    }


    public function meta()
    {
        return $this->morphMany(ProcessMeta::class, 'model', null, 'lapp_id', 'lapp_id');
    }
    public function getMetaAttribute($key)
    {
        $meta = $this->meta()->where('meta_key', $key)->first();
        return $meta ? $meta->meta_value : null;
    }

    public function setMetaAttribute($key, $value)
    {
        $this->meta()->updateOrCreate(
            ['meta_key' => $key],
            ['meta_value' => $value]
        );
    }
    public function getMetaValue($key)
    {
        return optional($this->meta()->where('meta_key', $key)->first())->meta_value;
    }

    public function setMetaValue($key, $value)
    {
        $this->meta()->updateOrCreate(
            ['meta_key' => $key],
            ['meta_value' => $value]
        );
    }
}
