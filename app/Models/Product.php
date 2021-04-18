<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];


    public function variants(){
        return $this->belongsToMany(Variant::class, 'product_variants', 'variant_id')->withTimestamps();
    }

    public function product_variants_price(){
        return $this->hasMany(ProductVariantPrice::class);
    }

}
