<?php

namespace App\Repositories;

use App\Models\Language;
use App\Models\UserCatalogue;
use App\Repositories\Interfaces\LanguageReponsitoryInterface;


/**
 * Class UserService
 * @package App\Services
 */
class LanguageReponsitory extends BaseRepository implements LanguageReponsitoryInterface
{
    protected $model;

    public function __construct(Language $model){
        $this->model = $model;
    }
}
