<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProgress extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'progress', 'status'];
}
