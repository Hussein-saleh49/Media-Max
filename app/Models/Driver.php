<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // تحديد الحقول التي يمكن ملؤها
    protected $fillable = ['name', 'email', 'phone'];

    // تحديد الحقول التي يجب إخفاؤها
    protected $hidden = ['password', 'remember_token'];

    // إضافة العلاقات إذا كانت موجودة
    public function orders()
    {
        return $this->hasMany(Order::class, 'delivered_by'); // إذا كنت تريد ربط الطلبات بالسائق
    }
}
