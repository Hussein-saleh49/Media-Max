<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $fillable = ['name', 'address', 'phone','image']; 

    public function medications()
    {
        return $this->belongsToMany(Medication::class)->withPivot('stock')->withTimestamps();
    }
}
