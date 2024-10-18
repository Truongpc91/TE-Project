<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, QueryScopes;

    protected $fillable = [
        'code',
        'fullName',
        'phone',
        'email',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'description',
        'promotion',
        'cart',
        'customer_id',
        'guest_cookie',
        'method',
        'confirm',
        'payment',
        'delivery',
        'shipping',
    ];

    protected $table = 'orders';

    protected $casts = [
        'promotion' => 'json',
        'cart' => 'json',
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'order_product' , 'order_id', 'product_id')
        ->withPivot(
            'uuid',
            'name',
            'qty',
            'price',
            'option',
        )->withTimestamps();
    }

    public function order_payments(){
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

}
