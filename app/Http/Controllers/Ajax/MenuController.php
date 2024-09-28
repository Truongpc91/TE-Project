<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Models\Language;
use App\Repositories\Interfaces\MenuReponsitoryInterface  as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface  as MenuCatalogueReponsitory;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;
use App\Services\Interfaces\MenuServiceInterface as MenuService;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuRepository;
    protected $menuCatalogueService;
    protected $menuService;
    protected $menuCatalogueReponsitory;
    protected $language;

    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueService $menuCatalogueService,
        MenuService $menuService,
        MenuCatalogueReponsitory $menuCatalogueReponsitory
    ) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->menuService = $menuService;
        $this->menuCatalogueReponsitory = $menuCatalogueReponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function createCatalogue(StoreMenuCatalogueRequest $request){
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if ($menuCatalogue !== FALSE) {
            return response()->json([
                'code' => 0,
                'message' => 'Tạo nhóm menu thành công',
                'data' => $menuCatalogue 
            ]);
        }
        return response()->json([
            'message' => 'Có vấn đề xảy ra xin hãy thử lại',
            'code' => 1
        ]);
    }

    public function drag(Request $request){
        $json = json_decode($request->input('json'), TRUE);
        $menuCatalogueId = json_decode($request->input('menu_catalogue_id'));

        $flag = $this->menuService->dragUpdate($json, $menuCatalogueId, $this->language);
    }   
}
