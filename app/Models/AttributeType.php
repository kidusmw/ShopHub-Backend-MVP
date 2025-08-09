<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeOption;

class AttributeType extends Model {
    protected $fillable = ['name','unit','category_id'];
    public function options() { return $this->hasMany(AttributeOption::class); }
}
