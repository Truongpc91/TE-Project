<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class PostCatalogue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
        'follow'
    ];

    protected $table = 'post_catalogues';

    public function languages(){
        return $this->belongsToMany(Language::class, 'post_catalogue_language' , 'post_catalogue_id', 'language_id')
        ->withPivot(
            'post_catalogue_id',
            'language_id',
            'name',
            'canonical',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'description',
            'content'
        )->withTimestamps();
    }

    public function posts(){
        return $this->belongsToMany(Post::class, 'post_catalogue_post' , 'post_catalogue_id', 'post_id');
    }


    public function post_catalogue_language(){
        return $this->hasMany(PostCatalogueLanguage::class, 'post_catalogue_id', 'id')->where('language_id','=',1);
    }

    public static function isNodeCheck($postCatalogue){
        
        // $postCatalogue = PostCatalogue::find($id);
        if($postCatalogue->rgt - $postCatalogue->lft !== 1){
            return false;
        } 

        return true;
        
    }
}
