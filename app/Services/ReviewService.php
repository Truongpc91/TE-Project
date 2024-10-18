<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\Interfaces\ReviewReponsitoryInterface as ReviewReponsitory;
use Illuminate\Support\Facades\DB;
use App\Classes\ReviewNested;

use App\Services\Interfaces\ReviewServiceInterface;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class ReviewService extends BaseService implements ReviewServiceInterface
{


    protected $ReviewReponsitory;
    
    public function __construct(
        ReviewReponsitory $ReviewReponsitory,
    ){
        $this->ReviewReponsitory = $ReviewReponsitory;
    }


    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except('_token');
            // dd($payload);
            $review = $this->ReviewReponsitory->create($payload);
            $this->reviewNestedset = new ReviewNested([
                'table' => 'reviews',
                'reviewable_type' => $payload['reviewable_type']
            ]);
            $this->reviewNestedset->Get('level ASC, order ASC');
            $this->reviewNestedset->Recursive(0, $this->reviewNestedset->Set());
            $this->reviewNestedset->Action();

            DB::commit();
            return [
                'code' => 10,
                'message' => 'Đánh giá sản phẩm thành công'
            ];
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return [
                'code' => 11,
                'message' => 'Đánh giá sản phẩm không thành công'
            ];
        }
    }

    

}
