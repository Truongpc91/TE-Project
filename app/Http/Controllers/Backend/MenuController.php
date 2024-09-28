<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuChildrenRequest;
use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;

use App\Repositories\Interfaces\MenuReponsitoryInterface as MenuReponsitory;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;
use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface  as MenuCatalogueReponsitory;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Language;
use App\Models\MenuCatalogue;

class MenuController extends Controller
{

    protected $MenuService;
    protected $MenuCatalogueService;
    protected $MenuReponsitory;
    protected $LanguageReponsitory;
    protected $menuCatalogueReponsitory;

    public function __construct(
        MenuService $MenuService,
        MenuCatalogueService $MenuCatalogueService,
        MenuReponsitory $MenuReponsitory,
        LanguageReponsitory $LanguageReponsitory,
        MenuCatalogueReponsitory $menuCatalogueReponsitory
    )
    {
        $this->MenuService = $MenuService;
        $this->MenuCatalogueService = $MenuCatalogueService;
        $this->MenuReponsitory = $MenuReponsitory;
        $this->LanguageReponsitory = $LanguageReponsitory;
        $this->menuCatalogueReponsitory = $menuCatalogueReponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.menus.index');
        $menuCatalogues = $this->MenuCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];

        $config['seo'] = __('messages.menu');

        $template = 'backend.menu.menu.index';
        // dd($template);

        return view('backend.dashboard.layout', compact('template', 'config', 'menuCatalogues'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.menus.create');
        $menuCatalogues = $this->menuCatalogueReponsitory->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 
                'backend/library/menu.js',
            ],
            'model'=> 'MenuCatalogue'
        ];

        $config['seo'] = __('messages.menu');
        $config['method'] = 'create';
        $template = 'backend.menu.menu.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues'
        ));
    }

    public function store(StoreMenuRequest $request)
    {
        if ($this->MenuService->save($request,$this->language)) {
            $menuCatalogueId = $request->input('menu_catalogue_id');
            return redirect()->route('admin.menu.edit', $menuCatalogueId)->with('success', 'Thêm mới Menu thành công !');
        } else {
            return redirect()->route('admin.menu.index')->with('error', 'Thêm mới Menu thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        // dd(123);
        $this->authorize('modules', 'admin.menus.update');
        $language = $this->language;
        $menuCatalogue = $this->menuCatalogueReponsitory->findById($id);
        $menus = $this->MenuReponsitory->findByCondition([
            ['menu_catalogue_id', '=', $id]
        ],TRUE, [
            'languages' => function($query) use ($language){
                $query->where('language_id', $language);
            }
        ], ['order', 'DESC']);

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/menu.js',
                'backend/js/plugins/nestable/jquery.nestable.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ]
        ];

        $config['seo'] = __('messages.menu');
        $config['method'] = 'edit';
        $template = 'backend.menu.menu.show';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menus',
            'menuCatalogue',
            'id'
        ));
    }

    public function editMenu($id) {
        $this->authorize('modules', 'admin.menus.update');
        $language = $this->language;
        $menuCatalogues = $this->menuCatalogueReponsitory->all();
        $menuCatalogue = $this->menuCatalogueReponsitory->findById($id);
        $menus = $this->MenuReponsitory->findByCondition([
            ['menu_catalogue_id', '=', $id],
            ['parent_id', '=', 0]

        ],TRUE, [
            'languages' => function($query) use ($language){
                $query->where('language_id', $language);
            }
        ], ['order', 'DESC']);
        $menuList = $this->MenuService->convertMenu($menus);

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/menu.js',
                'backend/js/plugins/nestable/jquery.nestable.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ]
        ];

        $config['seo'] = __('messages.menu');
        $config['method'] = 'update';
        $template = 'backend.menu.menu.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuList',
            'menuCatalogues',
            'menuCatalogue'
        ));
    }

    public function udpate(UpdateUserRequest $request, Menu $menu)
    {

        if ($this->MenuService->save($request)) {
            return redirect()->route('admin.menus.index')->with('success', 'Cập nhật Menu thành công !');
        } else {
            return redirect()->route('admin.menus.index')->with('error', 'Cập nhật Menu thất bại! Hãy thử lại');
        }
    }

    public function delete($id)
    {
        $this->authorize('modules', 'admin.menus.destroy');
        $menuCatalogue = $this->menuCatalogueReponsitory->findById($id);
        $template = 'backend.menu.menu.delete';
        $config['seo'] = __('messages.menu');
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogue'
        ));
    }

    public function destroy($id)
    {

        if ($this->MenuService->destroy($id)) {
            return redirect()->route('admin.menu.index')->with('success', 'Xóa Menu thành công !');
        } else {
            return redirect()->route('admin.menu.index')->with('error', 'Xóa Menu thất bại! Hãy thử lại');
        }
    }

    public function children($id){
        $this->authorize('modules', 'admin.menus.create');
        $language = $this->language;
        $menu = $this->MenuReponsitory->findById($id, ['*'], [
            'languages' => function($query) use ($language){
                $query->where('language_id', $language);
            }
        ]);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 
                'backend/library/menu.js',
            ],
            'model'=> 'MenuCatalogue'
        ];
        
        $menuList = $this->MenuService->getAndConvertMenu($menu, $this->language);
        $config['seo'] = __('messages.menu');
        $config['method'] = 'children';
        $template = 'backend.menu.menu.children';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu',
            'menuList'
        ));
    }

    public function saveChildren(StoreMenuChildrenRequest $request, $id){
        $menu = $this->MenuReponsitory->findById($id);
        if ($this->MenuService->saveChildren($request,$this->language, $menu)) {
            return redirect()->route('admin.menu.edit', $menu->menu_catalogue_id)->with('success', 'Thêm mới Menu thành công !');
        } else {
            return redirect()->route('admin.menu.edit', $menu->menu_catalogue_id)->with('error', 'Thêm mới Menu thất bại! Hãy thử lại');
        }
    }

    public function translate(int $languageId = 3, $id){
        $menuCatalogue = $this->menuCatalogueReponsitory->findById($id);
        $language = $this->LanguageReponsitory->findById($languageId);
        $currentLanguage = $this->language;
        $menus = $this->MenuReponsitory->findByCondition([
            ['menu_catalogue_id', '=', $id],
        ],TRUE, [
            'languages' => function($query) use ($currentLanguage){
                $query->where('language_id', $currentLanguage);
            }
        ], ['lft', 'ASC']);
        
        $menus = buildMenu($this->MenuService->findMenuItemTranslate($menus, $currentLanguage, $languageId));

        $config['seo'] = __('messages.menu');
        $config['method'] = 'translate';
        $template = 'backend.menu.menu.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language',
            'languageId',
            'menuCatalogue',
            'menus'
        ));
    }

    public function saveTranslate(Request $request, $languageId = 3) {
        if ($this->MenuService->saveTranslateMenu($request, $languageId)) {
            return redirect()->route('admin.menu.index')->with('success', 'Dịch Menu thành công !');
        } else {
            return redirect()->route('admin.menu.index')->with('error', 'Dịch Menu thất bại! Hãy thử lại');
        }
    }

    // private function config(){
    //     return [
    //         'js' => [
    //             'backend/js/plugins/switchery/switchery.js'
    //         ],
    //         'css' => [
    //             'backend/css/plugins/switchery/switchery.css'
    //         ]
    //     ];
    // }
}
