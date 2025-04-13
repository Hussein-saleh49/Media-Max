<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // المستخدم الذي قام بالتقييم
        'driver_id', // السائق الذي تم تقييمه
        'rating',
        'feedback'
    ];

    // علاقة التقييم بالسائق
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // علاقة التقييم بالمستخدم الذي قام بالتقييم
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
