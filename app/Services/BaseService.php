<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface;

/**
 * Class UserService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{

    public function __construct() {}

    public function currenLanguage()
    {
        return 1;
    }
}
