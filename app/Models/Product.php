<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory , sluggable;
    protected $table="products";
    protected $guarded =[];
    public function images():HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tag::class,'product_tag');
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
