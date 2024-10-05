<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widget extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'name',
        'keyword',
        'description',
        'short_code',
        'model',
        'publish',
        'model_id'
    ];


    protected $table = 'widgets';

    protected $casts = [
        'model_id' => 'json',
        'description' => 'json'
    ];

}
