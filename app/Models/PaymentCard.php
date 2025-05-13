<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'card_holder_name', 'card_number', 'expiry_date', 'cvv', 'card_type'
    ];

    protected $hidden = ['card_number', 'cvv'];

    // تشفير بيانات البطاقة عند حفظها
    public function setCardNumberAttribute($value)
    {
        $this->attributes['card_number'] = encrypt($value);
    }

    public function getCardNumberAttribute($value)
    {
        return decrypt($value);
    }

    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = encrypt($value);
    }

    public function getCvvAttribute($value)
    {
        return decrypt($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
