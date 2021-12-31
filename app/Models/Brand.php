<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getCategoriesAttribute()
    {
        $categories = $this->products()
        ->with(['brand' => function ($query) {
            $query->where('brands.id', $this->id);
        }])->get()
            ->pluck('category_id')->flatten();
        $categoriesData = Category::whereIn('id', $categories)->get();
        return $categoriesData;
    }
}
