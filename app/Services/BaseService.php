<?php

namespace App\Services;

use App\Models\Language;
use App\Services\Interfaces\BaseServiceInterface;

/**
 * Class UserService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    protected $language;

    public function __construct() {}

    public function currenLanguage()
    {
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        return $language->id;
    }
}
