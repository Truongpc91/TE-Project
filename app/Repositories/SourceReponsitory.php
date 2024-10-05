<?php

namespace App\Repositories;

use App\Models\Source;
use App\Repositories\Interfaces\SourceReponsitoryInterface;


/**
 * Class WidgetService
 * @package App\Services
 */
class SourceReponsitory extends BaseRepository implements SourceReponsitoryInterface
{
    protected $model;

    public function __construct(Source $model)
    {
        $this->model = $model;
    }

   
}
