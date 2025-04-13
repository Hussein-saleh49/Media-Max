<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount', 'type', 'usage_limit', 'used_count', 'expiration_date'
    ];

    public function isValid()
    {
        return $this->expiration_date > now() && $this->used_count < $this->usage_limit;
    }
}
