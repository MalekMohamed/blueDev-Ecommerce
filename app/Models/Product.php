<?php

namespace App\Models;

use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use BinaryCats\Sku\Concerns\SkuOptions;

class Product extends Model
{
    use HasFactory,SoftDeletes,HasSku;
    protected $guarded = [];

    public function skuOptions() : SkuOptions
    {
        return SkuOptions::make()
            ->from(['price', 'name'])
            ->target('sku')
            ->using('_')
            ->forceUnique(true)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }

    public function user() {
        return $this->belongsTo(User::class)->select(['id','name']);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
}
