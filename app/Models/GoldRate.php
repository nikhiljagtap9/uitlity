<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldRate extends Model
{
    use HasFactory;

    protected $table = 'gold_rates';
    protected $guarded = [];

}

