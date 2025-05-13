<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'final_price',
        'image',
        'rating',
    ];

    // حساب السعر النهائي تلقائيًا عند ضبط السعر أو الخصم
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        $discount = $this->attributes['discount'] ?? 0; // إذا لم يكن هناك خصم، اعتبره 0
        $this->attributes['final_price'] = max(0, $value - $discount); // التأكد من أن السعر النهائي لا يكون سالبًا
    }

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = $value;
        $price = $this->attributes['price'] ?? 0; // إذا لم يكن هناك سعر، اعتبره 0
        $this->attributes['final_price'] = max(0, $price - $value); // التأكد من أن السعر النهائي لا يكون سالبًا
    }
}
