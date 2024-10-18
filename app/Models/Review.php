<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'email',
        'gender',
        'fullname',
        'phone',
        'description',
        'score',
    ];

    protected $table = "reviews";

    public function reviewable()
    {
        return $this->morphTo();
    }
}
