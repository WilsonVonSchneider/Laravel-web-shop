<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
        'price',
        'sku',
        'published'
    ];

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public static function generateSku() {
        $datePart = date('Ymd'); 
        $randomPart = mt_rand(1000, 9999); 
        
        $sku = 'SKU-' . $datePart . '-' . $randomPart; 
        
        return $sku;
    }
}
