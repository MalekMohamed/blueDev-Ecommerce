<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($category) {

            $category->products()->delete();
        });
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getBrandsAttribute()
    {
        $brands = $this->products()
        ->with(['category' => function ($query) {
            $query->where('categories.id', $this->id);
        }])->get()->pluck('brand_id')->flatten();
        $brandsData = Brand::whereIn('id', $brands)->get();
        return $brandsData;
    }
}
