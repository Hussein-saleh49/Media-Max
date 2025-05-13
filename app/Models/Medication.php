<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'active_ingredient',
        'description',
        'warnings',
        'manufacturer',
        'dosage_form',
        'category',
        'search_count',
        'price',
    ];

    public function pharmacies()
{
    return $this->belongsToMany(Pharmacy::class)->withPivot('stock')->withTimestamps();
}

}
