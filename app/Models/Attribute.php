<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    use HasFactory;

    protected $table="attributes";
    protected $guarded =[];

    public function categories() : BelongsToMany
    {
        return $this->belongsToMany( Category::class, 'attribute_category');
    }
}
