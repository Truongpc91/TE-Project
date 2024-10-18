<?php

namespace App\Http\ViewComposers;
use App\Repositories\Interfaces\SystemReponsitoryInterface as SystemReponsitory;
use Illuminate\View\View;

class SystemComposer
{
    protected $language;
    protected $SystemReponsitory;

    public function __construct(SystemReponsitory $SystemReponsitory, $language)
    {
        $this->SystemReponsitory = $SystemReponsitory;
        $this->language = $language;
    }

    public function compose(View $view) {
        $system = $this->SystemReponsitory->findByCondition([
            ['language_id', '=', $this->language]
        ], TRUE);
        $systemArray = convert_array($system, 'keyword', 'content');
        $view->with('system', $systemArray);
    }
}