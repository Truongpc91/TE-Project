<?php

namespace App\Http\ViewComposers;

use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;
use Illuminate\View\View;

class LanguageComposer
{
    protected $language;
    protected $LanguageReponsitory;

    public function __construct(LanguageReponsitory $LanguageReponsitory, $language)
    {
        $this->LanguageReponsitory = $LanguageReponsitory;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $arguments = $this->argument();
        $languages = $this->LanguageReponsitory->findByCondition(...$arguments);
        // dd($languages);
        $view->with('languages', $languages);
    }

    private function argument(){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => TRUE,
            'relation' => [],
            'orderBy' => [
                'current', 'DESC'
            ]
        ];
    }
}
