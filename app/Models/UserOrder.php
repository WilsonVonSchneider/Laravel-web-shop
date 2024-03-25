<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    use HasUuids, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'phone',
        'address',
        'city_country',
        'total',
        'tax',
        'tax_amount',
        'total_with_tx'
    ];

    public function items()
    {
        return $this->hasMany(UserOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
