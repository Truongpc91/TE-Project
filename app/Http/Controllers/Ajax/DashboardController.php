<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface as UserService;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $userService;
    protected $language;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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

        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $flag = $serviceInstance->updateStatusAll($post);

        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $page = $request->input('page') ?? 1;
        $keyword = $request->string('keyword') ?? null;

        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Reponsitory';

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

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
}
