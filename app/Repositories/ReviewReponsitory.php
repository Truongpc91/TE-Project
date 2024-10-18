<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ReviewReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class ReviewReponsitory extends BaseRepository implements ReviewReponsitoryInterface
{
    protected $model;

    public function __construct(
        Review $model
    ){
        $this->model = $model;
    }

    public function getAttributeCatalogueById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'attribute_catalogues.id',
                'attribute_catalogues.parent_id',
                'attribute_catalogues.image',
                'attribute_catalogues.icon',
                'attribute_catalogues.album',
                'attribute_catalogues.publish',
                'attribute_catalogues.follow',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('attribute_catalogue_language as tb2', 'tb2.attribute_catalogue_id', '=','attribute_catalogues.id')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function getAll(int $languageId = 0){
        // dd( $this->model->with(['attribute_catalogue_language' => function($query) use ($languageId){
        //     // $query->where('language_id', $languageId);
        // }, ])->get());
        return $this->model->with(['attribute_catalogue_language' => function($query) use ($languageId){
            // $query->where('language_id', $languageId);
        }, ])->get();

    }

    public function getAttributeCatalogueWhereIn($whereIn = [], $whereInField = 'id', $language){
        return $this->model->select([
            'attribute_catalogues.id',
            'tb2.name',
        ])
        ->join('attribute_catalogue_language as tb2', 'tb2.attribute_catalogue_id', '=','attribute_catalogues.id')
        ->where('tb2.language_id', '=', $language)
        ->where([config('apps.general.defaultPublish')])
        ->whereIn( $whereInField, $whereIn)
        ->get();
    }

    public function getReviewByproduct($productId, $reviewable_type, $parent_id = 0) {
        return $this->model
        ->where('reviewable_id', $productId)
        ->where('reviewable_type', $reviewable_type)
        ->where('parent_id', $parent_id)
        ->get();
    }

}
