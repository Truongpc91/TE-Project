<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuReponsitoryInterface as MenuReponsitory;
use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface as MenuCatalogueReponsitory;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterReponsitory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use Illuminate\Support\Str;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuReponsitory;
    protected $routerReponsitory;
    protected $menuCatalogueReponsitory;
    protected $nestedset;


    public function __construct(
        MenuReponsitory $menuReponsitory,
        MenuCatalogueReponsitory $menuCatalogueReponsitory,
        RouterReponsitory $routerReponsitory
    ) {
        $this->menuReponsitory = $menuReponsitory;
        $this->menuCatalogueReponsitory = $menuCatalogueReponsitory;
        $this->routerReponsitory = $routerReponsitory;
    }

    public function paginate($request, $languageId)
    {
        return [];
    }

    private function initialize($languageId)
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => true,
            'language_id' => $languageId,
        ]);
    }

    public function save($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('menu', 'menu_catalogue_id', 'type');
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::user()->id
                    ];
                    if ($menuId == 0) {
                        $menuSave = $this->menuReponsitory->create($menuArray);
                    } else {
                        $menuSave = $this->menuReponsitory->update($menuId, $menuArray);
                        if ($menuSave->rgt - $menuSave->lft > 1) {
                            $this->menuReponsitory->updateByWhere([
                                ['lft', '>', $menuSave->lft],
                                ['rgt', '<', $menuSave->rgt],
                            ], ['menu_catalogue_id' => $payload['menu_catalogue_id']]);
                        }
                    }


                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payloadLanguage = [
                            'menu_id' => $menuSave->id,
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];

                        $this->menuReponsitory->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }
                $this->initialize($languageId);
                $this->nestedset();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function saveChildren($request, $languageId, $menu)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('menu');
            // dd($menu);
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $menu->menu_catalogue_id,
                        'parent_id' => $menu->id,
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::user()->id
                    ];
                    // dd($menuId);
                    $menuSave = ($menuId == 0)
                        ? $menuSave = $this->menuReponsitory->create($menuArray)
                        : $menuSave = $this->menuReponsitory->update($menuId, $menuArray);

                    // dd($menuSave);
                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];

                        $this->menuReponsitory->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }
                $this->initialize($languageId);
                $this->nestedset();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $this->menuReponsitory->forceDeleteByCondition([
                ['menu_catalogue_id', '=', $id]
            ]);
            $this->menuCatalogueReponsitory->forceDelete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function nestedset()
    {
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    public function getAndConvertMenu($menu = null, $language = 3): array
    {
        $menuList = $this->menuReponsitory->findByCondition([
            ['parent_id', '=', $menu->id]
        ], TRUE, [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ]);

        return $temp = $this->convertMenu($menuList);
    }

    public function convertMenu($menuList = null)
    {
        $temp = [];
        $fields = ['name', 'canonical', 'order', 'id'];
        if (count($menuList)) {
            foreach ($menuList as $key => $val) {
                foreach ($fields as $field) {
                    if ($field == 'name' || $field == 'canonical') {
                        $temp[$field][] = $val->languages->first()->pivot->{$field};
                    } else {
                        $temp[$field][] = $val->{$field};
                    }
                }
            }
        }

        return $temp;
    }

    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, $languageId = 3, int $parentId = 0)
    {
        if (count($json)) {
            foreach ($json as $key => $val) {
                $update = [
                    'order' => count($json) - $key,
                    'parent_id' => $parentId
                ];

                $menu = $this->menuReponsitory->update($val['id'], $update);
                if (isset($val['children']) && count($val['children'])) {
                    // dd($val['id']);
                    $this->dragUpdate($val['children'], $menuCatalogueId, $languageId, $val['id']);
                }
            }
        }
        $this->initialize($languageId);
        $this->nestedset();
    }

    public function findMenuItemTranslate($menus, int $currentLanguage = 3, int $languageId = 3)
    {
        $output = [];
        if (count($menus)) {
            foreach ($menus as $menu) {
                $canonical = $menu->languages->first()->pivot->canonical;

                $detailMenu = $this->menuReponsitory->findById($menu->id, ['*'], [
                    'languages' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }
                ]);

                if ($detailMenu) {
                    if($detailMenu->languages->isNotEmpty()){
                        $menu->translate_name = $detailMenu->languages->first()->pivot->name;
                        $menu->translate_canonical = $detailMenu->languages->first()->pivot->canonical;
                    }else {
                        $router = $this->routerReponsitory->findByCondition(
                            [['canonical', '=', $canonical]]
                        );
                        if ($router) {
                            $controller = explode('\\', $router->controllers);
                            $model = str_replace('Controller', '', end($controller));
    
                            $serviceInterfaceNamespace = '\App\Repositories\\' . $model . 'Reponsitory';
                            if (class_exists($serviceInterfaceNamespace)) {
                                $serviceInstance = app($serviceInterfaceNamespace);
                            }
                            $alias = Str::snake($model) . '_language';
                            // dd($model);
                            $object = $serviceInstance->findByWhereHas([
                                'canonical' => $canonical,
                                'language_id' => $currentLanguage,
    
                            ], 'languages', $alias);
    
    
                            if ($object) {
                                $translateObject = $object->languages()->where('language_id', $languageId)->first([$alias . '.name', $alias . '.canonical']);
                                if (!is_null($translateObject)) {
                                    $menu->translate_name = $translateObject->name;
                                    $menu->translate_canonical = $translateObject->canonical;
                                }
                            }
                        }
                    }
                } 
                $output[] = $menu;
                
            }
        }
        // dd($output);
        return $output;
    }

    public function saveTranslateMenu($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('translate');
            // dd($payload);
            if (count($payload['translate']['name'])) {
                foreach ($payload['translate']['name'] as $key => $val) {
                    if ($val == null) continue;
                    $temp = [
                        'language_id' => $languageId,
                        'name' => $val,
                        'canonical' => $payload['translate']['canonical'][$key],
                    ];
                    $menu = $this->menuReponsitory->findById($payload['translate']['id'][$key]);
                    $menu->languages()->detach($languageId);
                    $this->menuReponsitory->createPivot($menu, $temp, 'languages');
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }
}
