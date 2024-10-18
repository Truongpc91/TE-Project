<?php

namespace App\Http\ViewComposers;

use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface as MenuCatalogueReponsitory;
use Illuminate\View\View;

class MenuComposer
{
    protected $language;
    protected $MenuCatalogueReponsitory;

    public function __construct(MenuCatalogueReponsitory $MenuCatalogueReponsitory, $language)
    {
        $this->MenuCatalogueReponsitory = $MenuCatalogueReponsitory;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        
        $arguments = $this->arguments($this->language);
        $menuCatalogue = $this->MenuCatalogueReponsitory->findByCondition(...$arguments);
        $menus = [];
        $htmlType = ['main-menu'];
        if(count($menuCatalogue)){
            foreach ($menuCatalogue as $key => $val) {
                $type = (in_array($val->keyword, $htmlType)) ? 'html' : 'array';
                $menus[$val->keyword] = frontent_recursive_menu(recursive($val->menus), 0, 1, $type);
            }
        }

        $view->with('menus', $menus);
    }

    private function arguments($language)
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => TRUE,
            'relation' => [
                'menus' => function ($query) use ($language) {
                    $query->orderBy('order', 'DESC');
                    $query->with([
                        'languages' => function ($query) use ($language) {
                            $query->where('language_id', $language);
                        }
                    ]);
                }
            ]
        ];
    }
}
