<?php

namespace App\Services;

use App\Enums\PromotionEnums;
use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as promotionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * Class UserService
 * @package App\Services
 */
class PromotionService implements PromotionServiceInterface
{
    protected $promotionRepository;

    public function __construct(promotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function paginate($request, $languageId)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $promotions = $this->promotionRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/promotion/index'],
            ['id', 'DESC'],
            [],
            [],
        );
        return $promotions;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);

            $promotion = $this->promotionRepository->create($payload);
            // dd($promotion);
            if ($promotion->id > 0) {
                $this->handleRelation($request,  $promotion);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id, $languageId)
    {
        DB::beginTransaction();
        try {

            $payload = $this->request($request);
            $promotion = $this->promotionRepository->update($id, $payload);
            if ($promotion->id > 0) {
                $this->handleRelation($request, $promotion, 'update');
            }
           
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }


    private function handleRelation($request, $promotion, $method = 'create'){
        if($request->input('method') === PromotionEnums::PRODUCT_AND_QUANTITY){
            $object = $request->input('object');
            // dd($request);
            $payloadRelations = [];
            foreach ($object['id'] as $key => $val) {
                $payloadRelations[] = [
                    'product_id'   => $val,
                    'variant_uuid' => $object['variant_uuid'][$key],
                    'model' => $request->input(PromotionEnums::MODULE_TYPE)
                ];
            }
            // dd($payloadRelations);
            if($method == 'update'){
                $promotion->products()->detach([]);
            }
            $promotion->products()->sync($payloadRelations);
        }
    }

    // private function createPromotionProductVariant($request, $promotion)
    // {
    //     $object = $request->input('object');
    //     $payloadRelations = [];
    //     foreach ($object['id'] as $key => $val) {
    //         $payloadRelations[] = [
    //             'product_id'   => $val,
    //             'product_variant_id' => $object['product_variant_id'][$key],
    //             'model' => $request->input(PromotionEnums::MODULE_TYPE)
    //         ];
    //     }
    //     $promotion->products()->sync($payloadRelations);
    // }

    private function request($request){
        $payload = $request->only('name', 'code', 'description', 'method', 'startDate', 'endDate', 'neverEndDate');
        $payload['discountValue'] = $request->input(PromotionEnums::PRODUCT_AND_QUANTITY.'.discountValue');
        $payload['discountType'] = $request->input(PromotionEnums::PRODUCT_AND_QUANTITY.'.discountType');
        $payload['maxDiscountValue'] = $request->input(PromotionEnums::PRODUCT_AND_QUANTITY.'.maxDiscountValue');
        if (is_null($payload['discountValue'])) {
            $payload['discountValue'] =  0;
        }
        if (is_null($payload['discountType'])) {
            $payload['discountType'] =  '';
        }
        if (is_null($payload['maxDiscountValue'])) {
            $payload['maxDiscountValue'] =  0;
        }
        // dd($payload);
        $payload['startDate'] = Carbon::createFromFormat('d/m/Y H:i',$payload['startDate']);
        if(isset($payload['endDate'])){
            $payload['endDate'] = Carbon::createFromFormat('d/m/Y H:i',$payload['endDate']);
        }
        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];
        // dd($payload);
        switch ($payload['method']) {
            case PromotionEnums::ORDER_AMOUNT_RANGE:
                $payload[PromotionEnums::DISCOUNT] = $this->orderByRange($request);
                break;
            case PromotionEnums::PRODUCT_AND_QUANTITY:
                $payload[PromotionEnums::DISCOUNT] = $this->productAndQuantity($request);
                break;
        }
        // dd($payload);
        return $payload;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $promotion = $this->promotionRepository->findById($id);
            $destroyUser = $this->promotionRepository->destroy($promotion);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);

            $updateUser = $this->promotionRepository->update($user, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];

            $flag = $this->promotionRepository->updateByWhereIn('id', $post['id'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function saveTranslate($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $temp = [];
            $translateId = $request->input('translateId');
            $promotion = $this->promotionRepository->findById($request->input('widgetId'));
            $temp = $promotion->description;
            $temp[$translateId] = $request->input('translate_description');
            $payload['description'] =  $temp;

            $this->promotionRepository->update($promotion->id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function handleSourceAndCondition($request)
    {
       
        $data =  [
            'source' => [
                'status' => $request->input('source'),
                'data' => $request->input('sourceValue'),
            ],
            'apply' => [
                'status' => $request->input('applyStatus'),
                'data' => $request->input('applyValue'),
            ]
        ];
       

        if(!is_null($data['apply']['data'])){
            foreach ($data['apply']['data'] as $key => $val) {
                $data['apply']['condition'][$val] = $request->input($val);
            }
        }

        return $data;
    }

    private function orderByRange($request)
    {
        $data['info'] = $request->input('promotion_order_amount_range');
        // dd($data + $this->handleSourceAndCondition($request));
        return $data + $this->handleSourceAndCondition($request);
    }

    private function productAndQuantity($request)
    {
        $data['info'] = $request->input('product_and_quantity');
        $data['info']['model'] = $request->input(PromotionEnums::MODULE_TYPE);
        $data['info']['object'] = $request->input('object');
        return $data + $this->handleSourceAndCondition($request);
    }
}
