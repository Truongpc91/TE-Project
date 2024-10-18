<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideResponsitory;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Enums\SlideEnums;

use App\Http\Controllers\FrontendController;


use Illuminate\Http\Request;

class HomeController extends FrontendController
{

    protected $language;
    protected $SlideResponsitory;
    protected $WidgetService;
    protected $SlideService;

    public function __construct(
        SlideResponsitory $SlideResponsitory,
        WidgetService $WidgetService,
        SlideService $SlideService,
        
    ) {
        parent::__construct();
        $this->SlideResponsitory = $SlideResponsitory;
        $this->WidgetService = $WidgetService;
        $this->SlideService = $SlideService;
    }

    public function index()
    {
        $config = $this->config();

        $widget = $this->WidgetService->getWidget([
            ['keyword' => 'category','children' => true, 'promotion' => true, 'countObject' => true],
            ['keyword' => 'category-highlight', 'countObject' => true],
            ['keyword' => 'category-home', 'children' => true, 'promotion' => true],
            ['keyword' => 'bestseller']

        ], $this->language);
        // dd($widget['category']);
        $language = $this->language;
        $slides = $this->SlideService->getSlide([SlideEnums::BANNER, SlideEnums::MAIN], $this->language);
        $template = 'frontend.homepage.home.index';
        return view($template, compact(
            'template',
            'config',
            'slides',
            'widget',
            'language'
        ));
    }

    // private function slideArgument()
    // {
    //     return [
    //         'condition' => [
    //             config('apps.general.defaultPublish'),
    //             ['keyword', '=', 'main-slide']
    //         ]
    //     ];
    // }

    private function config()
    {
        return [];
    }
}
