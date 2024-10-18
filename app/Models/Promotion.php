<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'method',
        'discountInformation',
        'neverEndDate',
        'startDate',
        'endDate',
        'publish',
        'order',
        'discountValue',
        'discountType',
        'maxDiscountValue',
    ];

    protected $table = 'promotions';

    protected $casts = [
        'discountInformation' => 'json',
    ];

    // protected $attributes = [
    //     'neverEndDate' => 'json'
    // ];

    public function products(){
        return $this->belongsToMany(Product::class, 'promotion_product_variant' , 'promotion_id', 'product_id')
        ->withPivot(
            'variant_uuid',
            'model',
        )->withTimestamps();
    }

}
