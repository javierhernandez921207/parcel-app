<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipient extends Model
{
    protected $fillable = ['name', 'phone ', 'email', 'address'];

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }  
}
