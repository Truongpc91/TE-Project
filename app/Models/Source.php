<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'name',
        'keyword',
        'description',
        'publish',
    ];

    protected $table = 'sources';

    public function customers(){    
        return $this->hasMany(Customer::class, 'source_id', 'id');
    }
}
