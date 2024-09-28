<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slide extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'name',
        'description',
        'keyword',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'slide_language' , 'slide_id', 'language_id')
        ->withPivot(
            'menu_id',
            'language_id',
            'name',
            'canonical',
        )->withTimestamps();
    }
}
