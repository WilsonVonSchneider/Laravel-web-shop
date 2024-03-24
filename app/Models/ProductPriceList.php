<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductPriceList extends Model
{
    use HasUuids, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'description', 
        'sku',
        'active'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public static function generateSku() {
        $datePart = date('YmdHis'); 
        $milliseconds = microtime(true) * 1000; 
        $randomPart = mt_rand(1000, 9999); 
        
        $sku = 'SKU-' . $datePart . '-' . $milliseconds . '-' . $randomPart; 
        
        return $sku;
    }
}
