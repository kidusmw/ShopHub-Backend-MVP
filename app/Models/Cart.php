<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\CartItem;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_status'];

    /**
     * Cast the order_status to boolean for easier handling in the application.
    */
    protected $casts = [
        'order_status' => 'boolean',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
