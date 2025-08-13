<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model {
    protected $fillable = ['attribute_type_id','value'];
    
    public function type() { 
        return $this->belongsTo(AttributeType::class, 'attribute_type_id'); 
    }

    public function variants() {
        return $this->belongsToMany(Variant::class, 'variant_attribute_option');
    }
}

