<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'user_id',
        'keyword',
        'content',
    ];

    protected $table = 'systems';

    public function languages(){
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
