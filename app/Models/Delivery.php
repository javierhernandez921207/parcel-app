<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Delivery extends Model
{
    protected $fillable = ['status', 'carrier', 'tracking_number'];
    
    public function Parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    } 

    public function Recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }   

}
