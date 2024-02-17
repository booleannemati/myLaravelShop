<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table="categories";
    protected $guarded =[];

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class,'attribute_category');
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__,'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__,'parent_id');
    }
    public function getIsActiveAttribute($is_Active)
    {
        return $is_Active ? 'فعال' : 'غیرفعال' ;
    }

}
