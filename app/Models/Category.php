<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'priority',
        'product_count'
    ];

    // تحويل بعض الحقول إلى أنواع بيانات مناسبة
    protected $casts = [
        'priority' => 'integer',
        'product_count' => 'integer',
    ];

    // علاقة الفئة مع الأدوية (كل فئة تحتوي على عدة أدوية)
    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
