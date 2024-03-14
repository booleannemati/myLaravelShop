<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory , sluggable;
    protected $table="products";
    protected $guarded =[];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    //===================================================
    // relations
    public function images():HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tag::class,'product_tag');
    }

    public function brand():BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attributes():HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variations():HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    //===================================================
    // accessor
    public function getIsActiveAttribute($is_Active)
    {
        return $is_Active ? 'فعال' : 'غیرفعال' ;
    }
}
