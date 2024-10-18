<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as widgetRepository;
use App\Repositories\Interfaces\ProductCatalogueReponsitoryInterface as ProductCatalogueReponsitory;

use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Services\Interfaces\ProductServiceInterface as ProductService;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 * @package App\Services
 */
class WidgetService implements WidgetServiceInterface
{
    protected $widgetRepository;
    protected $PromotionReponsitory;
    protected $ProductCatalogueReponsitory;
    protected $ProductService;

    public function __construct(
        widgetRepository $widgetRepository,
        PromotionReponsitory $PromotionReponsitory,
        ProductCatalogueReponsitory $ProductCatalogueReponsitory,
        ProductService $ProductService,
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->ProductCatalogueReponsitory = $ProductCatalogueReponsitory;
        $this->ProductService = $ProductService;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $users = $this->widgetRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/widget/index'],
            ['id', 'DESC'],
            [],
            [],
        );
        // dd($users);
        return $users;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            // dd($payload);
            $widget = $this->widgetRepository->create($payload);
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

            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            // dd($payload);
            $widgetUpdate = $this->widgetRepository->update($id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $widget = $this->widgetRepository->findById($id);
            // dd($widget);
            $destroyUser = $this->widgetRepository->destroy($widget);
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

            $user = User::find($post['modelId']);

            $updateUser = $this->widgetRepository->update($user, $payload);
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

            $flag = $this->widgetRepository->updateByWhereIn('id', $post['id'], $payload);

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
            $widget = $this->widgetRepository->findById($request->input('widgetId'));
            $temp = $widget->description;
            $temp[$translateId] = $request->input('translate_description');
            $payload['description'] =  $temp;

            $this->widgetRepository->update($widget->id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = '')
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');

        return $birthday;
    }

    //* FRONTEND

    // public function findWidgetByKeyword(string $keyword = '', int $language = 3, array $param = [])
    // {
    //     $widget = $this->widgetRepository->findByCondition([
    //         ['keyword', '=', $keyword],
    //         config('apps.general.defaultPublish')
    //     ]);
    //     if (!is_null($widget)) {
    //         $class = loadClass($widget->model);
    //         $arguments = $this->widgetArgument($widget, $language, $param);
    //         $object = $class->findByCondition(...$arguments);
    //         $model = lcfirst(str_replace('Catalogue', '', $widget->model));
    //         if (count($object)) {
    //             foreach ($object as $key_1 => $val) {
    //                 // if($val->id != 4) continue;
    //                 if ($model === 'product' && isset($param['object']) && $param['object'] == true) {
    //                     $productId = $val->products->pluck('id')->toArray();
    //                     $val->products = $this->ProductService->combineProductAndPromotion($productId, $val->products);
    //                 }
    //                 if (isset($param['children']) && $param['children'] == true) {

    //                     $condition = [
    //                         ['lft', '>', $val->lft],
    //                         ['rgt', '<', $val->rgt],
    //                         config('apps.general.defaultPublish'),
    //                     ];
    //                     $val->childrens = $this->ProductCatalogueReponsitory->findByCondition($condition, TRUE);
    //                 }
    //             }
    //         }



    //         return $object;
    //     }
    // }



    public function getWidget(array $params = [], int $language)
    {
        $whereIn = [];
        $whereInField = 'keyword';

        if (count($params)) {
            foreach ($params as $key => $val) {
                $whereIn[] = $val['keyword'];
            }
        }
        $widgets = $this->widgetRepository->getWidgetByWhereIn($whereIn);
        $widgetArray = [];
        $temp = [];
        if (!is_null($widgets)) {
            foreach ($widgets as $key => $widget) {
              
                $class = loadClass($widget->model);
                $argument = $this->widgetArgument($widget, $language, $params[$key]);
                $object = $class->findByCondition(...$argument);
                // dd($object);
                $model = lcfirst(str_replace('Catalogue', '', $widget->model));
                $repalce = $model . 's';
                $service = ucfirst($model . 'Service');

                if (count($object) && strpos($widget->model, 'Catalogue')) {
                    $classRepo = loadClass(ucfirst($model));
                    foreach ($object as $objectKey => $objectValue) {
                        if (isset($params[$key]['children']) && $params[$key]['children']) {
                           
                            //* Lấy danh mục con cấp 1
                            $childrenArgument = $this->childrenArgument([$objectValue->id], $language);
                            $objectValue->childrens = $class->findByCondition(...$childrenArgument);
                        }
                       
                        $childId = $class->recursiveCatalogue($objectValue->id, $model);
                        $ids = [];
                        foreach ($childId as $child_id) {
                            $ids[] = $child_id->id;
                        }
                        // dd($ids);
                        if ($objectValue->rgt - $objectValue->lft > 1) {
                            $objectValue->{$repalce} = $classRepo->findObjectByCategoryId($ids, $model, $language);
                        }
                        if (isset($params[$key]['promotion']) && $params[$key]['promotion'] == true) {
                            $productId = $objectValue->{$repalce}->pluck('id')->toArray();
                            $objectValue->{$repalce} = $this->{$service}->combineProductAndPromotion($productId, $objectValue->{$repalce});
                        }
                        foreach ($objectValue->products as $keyObject => $valObject) {
                            $temp[$valObject->id] = $valObject;
                        }
                    }
                    $widgets[$key]->object = $object;
                    
                }else {
                    $productId = $object->pluck('id')->toArray();
                    $object = $this->{$service}->combineProductAndPromotion($productId, $object);
                    $widget->object = $object;
                }
                $widgetArray[$widget->keyword] = $widget;
            }
        }
        // dd($widgetArray);  
        return $widgetArray;
    }

    private function widgetArgument($widget, $language, $param)
    {
        $relation = [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ];

        $withCount = [];
       
        if (strpos($widget->model, 'Catalogue')) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            if(isset($param['object'])){
                $relation[$model] = function ($query) use ($param, $language) {
                    // $query->where('publish', 2);
                    $query->whereHas('languages', function ($query) use ($language) {
                        $query->where('language_id', $language);
                    });
                    $query->take(($param['limit']) ?? 100);
                    $query->orderBy('order', 'DESC');
                };
            }
            if (isset($param['countObject'])) {
                $withCount[] = $model;
            }
        }

        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => TRUE,
            'relation' => $relation,
            'param' => [
                'whereIn' => $widget->model_id,
                'whereInField' => 'id',
            ],
            'withCount' => $withCount
        ];
    }

    private function childrenArgument($objectId, $language)
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => TRUE,
            'relation' => [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ],
            'param' => [
                'whereIn' => $objectId,
                'whereInField' => 'parent_id'
            ]
        ];
    }
}


    

// $query->with('promotions', function ($query) use ($param) {
//     $query->select([
//         'promotions.id',
//         'promotions.discountValue',
//         'promotions.discountType',
//         'promotions.maxDiscountValue',
//         DB::raw(
//             "
//                 IF(promotions.maxDiscountValue != 0, 
//                      LEAST (
//                         CASE 
//                         WHEN discountType = 'cash' THEN (SELECT price FROM products WHERE products.id = product_id) - discountValue
//                         WHEN discountType = 'percent' THEN (SELECT price FROM products WHERE products.id = product_id) - ((SELECT price FROM 
//                         products WHERE products.id = product_id)*discountValue/100)
//                         ELSE (SELECT price FROM products WHERE products.id = product_id)
//                         END,
//                         promotions.maxDiscountValue
//                     ),

//                     CASE 
//                         WHEN discountType = 'cash' THEN (SELECT price FROM products WHERE products.id = product_id) - discountValue
//                         WHEN discountType = 'percent' THEN (SELECT price FROM products WHERE products.id = product_id) - ((SELECT price FROM 
//                         products WHERE products.id = product_id)*discountValue/100)
//                         ELSE (SELECT price FROM products WHERE products.id = product_id)
//                         END

//                 ) as discount
//             "
//         )
//     ]);
//     $query->where('publish', 1);
//     $query->where('endDate', '>', now());
//     $query->orderBy('discount', 'ASC');
//     $query->get();
// });