<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserOrderItem extends Model
{
    use HasFactory;
    use HasUuids, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id', 
        'order_id', 
        'price',
        'final_price',
    ];

    public function order()
    {
        return $this->belongsTo(UserOrder::class, 'user_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
