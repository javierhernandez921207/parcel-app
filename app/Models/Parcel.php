<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcel extends Model
{
    protected $fillable = ['width', 'height', 'length', 'weight', 'delivery_id'];

    public function Delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
