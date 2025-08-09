<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','category_id','title','description','price','discount_price','status'];

    public function vendor() { return $this->belongsTo(User::class, 'user_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function variants() { return $this->hasMany(Variant::class); }

    /**
     * Load all related models for the product.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function loadFull() {
        return $this->load([
            'category',
            'vendor',
            'images',
            'variants.attributeOptions.attributeType'
        ]);
    }
}
