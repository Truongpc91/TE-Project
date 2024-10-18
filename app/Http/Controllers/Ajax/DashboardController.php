<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Repositories\Interfaces\WidgetRepositoryInterface as widgetReponsitory;

use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $userService;
    protected $widgetReponsitory;
    protected $language;

    public function __construct(
        UserService $userService,
        widgetReponsitory $widgetReponsitory
    ) {
        $this->userService = $userService;
        $this->widgetReponsitory = $widgetReponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function changeStatus(Request $request)
    {
        $post = $request->input();

        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';

        // dd($serviceInterfaceNamespace);

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $flag = $serviceInstance->updateStatus($post);

        return response()->json(['flag' => $flag]);
    }

    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $serviceInstance = $this->loadClassInterface($post['model'], 'Service');

        $flag = $serviceInstance->updateStatusAll($post);

        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $page = $request->input('page') ?? 1;
        $keyword = $request->string('keyword') ?? null;
        $serviceInstance = $this->loadClassInterface($model,'Repositories', 'Reponsitory');

        $arguments = $this->paginationArgument($model, $keyword);
        $object = $serviceInstance->pagination(...array_values($arguments));

        return response()->json($object);
    }

    private function paginationArgument(string $model = '', string $keyword = ''): array
    {
        $model = Str::snake($model);
        $join = [
            [$model . '_language as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id'],
        ];
        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language],
            ],
            'keyword' => $keyword
        ];
        if (strpos($model, '_catalogue') === false) {
            $join[] = [$model . '_catalogue_' . $model . ' as tb3', $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }
        return [
            'select' => ['id', 'name', 'canonical'],
            'condition' => $condition,
            'per_page' => 10,
            'paginationConfig' => [
                'path' => $model . '.index',
                'groupBy' => ['id', 'name', 'canonical'],
            ],
            'orderBy' => [$model . 's.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }

    public function findModelObject(Request $request)
    {
        $model = $request->input('model');
        $keyword = $request->input('keyword');
        $alias = Str::snake($model) . '_language';
        $language = $this->language;
        $class = $this->loadClassInterface($model, 'Repositories', 'Reponsitory');
        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $keyword . '%'],
        ], $alias, $language);

        // dd($object);
        return response()->json($object);
    }

    private function loadClassInterface(string $model = '', $folder = 'Repositories', $interface = 'Reponsitory')
    {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        return $serviceInstance;
    }

    public function findPromotionObject(Request $request)
    {
        $get = $request->input();
        $keyword = $get['search'];
        // dd($keyword);
        $model = $get['option']['model'];
        $alias = Str::snake($model) . '_language';
        $language = $this->language;
        $class = $this->loadClassInterface($model, 'Repositories', 'Reponsitory');
        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $keyword . '%'],
        ], $alias, $language);

        $temp = [];
        if (count($object)) {
            foreach ($object as $key => $val) {
                $temp[] = [
                    'id' => $val->id,
                    'text' => $val->languages->first()->pivot->name
                ];
            }
        }
        return response()->json(array('items' => $temp));
    }

    public function getPromotionConditionValue(Request $request)
    {
        try {
            $get = $request->input();
            switch ($get['value']) {
                case 'staff_take_care_customer':
                    $class = loadClass('User');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_group':
                    $class = loadClass('CustomerCatalogue');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_gender':
                    $object = __('module.gender');
                    break;
                case 'customer_birthday':
                    $object = __('module.day');
                    break;
            }

            $temp = [];
            if (!is_null($object) && count($object)) {
                foreach ($object as $key => $val) {
                    $temp[] = [
                        'id' => $val['id'],
                        'text' => $val['name']
                    ];
                }
            }

            return response()->json([
                'data' => $temp,
                'error' => false,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
