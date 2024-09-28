<?php

namespace App\Services;

use App\Models\Language;
use App\Services\Interfaces\BaseServiceInterface;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterReponsitory;
use Illuminate\Support\Str;

/**
 * Class UserService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    protected $language;
    protected $controllerName;
    protected $routerReponsitory;

    public function __construct(RouterReponsitory $routerReponsitory,) {
        $this->routerReponsitory = $routerReponsitory;
    }

    public function currenLanguage()
    {
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        return $language->id;
    }

    public function formatRouterPayload($model, $request, $controllerName, $languageId){
        $router = [
            'canonical' => Str::slug($request->input('canonical')),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Backend\\'.$controllerName.'',
        ];
        // dd($router);
        return $router;
    }

    public function createRouter($model, $request, $controllerName, $languageId){
        // dd($this->routerReponsitory);
        $router = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        $this->routerReponsitory->create($router);
    }


    public function updateRouter($model, $request, $controllerName, $languageId){
        $payload = $this->formatRouterPayload($model, $request, $controllerName,  $languageId);
        $condition = [
            ['module_id','=', $model->id],
            ['controllers','=', 'App\Http\Controllers\Backend\\'.$controllerName],
        ];
        $router = $this->routerReponsitory->findByCondition($condition);
        $res = $this->routerReponsitory->update($router->id, $payload);
        
        return $res;
    }
}
