<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeOption;

use App\Models\Product;

class Variant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'status', // Check if this is needed
        'name',
        'sku',
        'stock',
        'price'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute options associated with the variant.
     */
    public function attributeOptions() {
        return $this->belongsToMany(AttributeOption::class, 'variant_attribute_option');
    }

    /**
     * Get the cart items associated with the variant.
     */
    public function cartItems() {
        return $this->hasMany(CartItem::class);
    }
}
