<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id', // السائق الذي تم تقييمه
        'rating',
        'feedback'
    ];

    // علاقة التقييم بالسائق
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
