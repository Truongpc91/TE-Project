<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Http\Request;

class RouterController extends FrontendController
{
    protected $language;
    protected $RouterRepository;

    
    public function __construct(  
        RouterRepository $RouterRepository
    ) {
        parent::__construct();
        $this->RouterRepository = $RouterRepository;
    }

    public function index(string $canonical = '', Request $request){
        $router = $this->RouterRepository->findByCondition([
            ['canonical', '=', $canonical],
            ['language_id','=', $this->language]
        ]);

        if(!is_null($router) && !empty($router)){
            $method = 'index';
            echo app($router->controllers)->{$method}($router->module_id, $request);
        }
    }

    public function page(string $canonical = '', int $page = 1, Request $request){
        (!isset($page)) ? 1 : $page;
        $router = $this->RouterRepository->findByCondition([
            ['canonical', '=', $canonical],
            ['language_id','=', $this->language]
        ]);
        if(!is_null($router) && !empty($router)){
            $method = 'index';
            echo app($router->controllers)->{$method}($router->module_id, $request, $page);
        }
    }

    
}
