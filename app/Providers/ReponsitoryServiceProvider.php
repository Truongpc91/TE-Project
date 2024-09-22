<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ReponsitoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * 
     */

     public $bindings = [
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' => 'App\Repositories\UserCatalogueRepository',
        'App\Repositories\Interfaces\PermissionReponsitoryInterface' => 'App\Repositories\PermissionReponsitory',
        'App\Repositories\Interfaces\PostCatalogueReponsitoryInterface' => 'App\Repositories\PostCatalogueReponsitory',
        'App\Repositories\Interfaces\LanguageReponsitoryInterface' => 'App\Repositories\LanguageReponsitory',
        'App\Repositories\Interfaces\GenerateReponsitoryInterface' => 'App\Repositories\GenerateReponsitory',
        'App\Repositories\Interfaces\PostReponsitoryInterface' => 'App\Repositories\PostReponsitory',
        'App\Repositories\Interfaces\ProvinceReponsitoryInterface' => 'App\Repositories\ProvinceReponsitory',
        'App\Repositories\Interfaces\DistrictReponsitoryInterface' => 'App\Repositories\DistrictReponsitory',
        'App\Repositories\Interfaces\ProductCatalogueReponsitoryInterface' => 'App\Repositories\ProductCatalogueReponsitory',
        'App\Repositories\Interfaces\ProductReponsitoryInterface' => 'App\Repositories\ProductReponsitory',
        'App\Repositories\Interfaces\AttributeCatalogueReponsitoryInterface' => 'App\Repositories\AttributeCatalogueReponsitory',
        'App\Repositories\Interfaces\AttributeReponsitoryInterface' => 'App\Repositories\AttributeReponsitory',
        'App\Repositories\Interfaces\ProductVariantReponsitoryInterface' => 'App\Repositories\ProductVariantReponsitory',
        'App\Repositories\Interfaces\ProductVariantLanguageReponsitoryInterface' => 'App\Repositories\ProductVariantLanguageReponsitory',
        'App\Repositories\Interfaces\ProductVariantAttributeReponsitoryInterface' => 'App\Repositories\ProductVariantAttributeReponsitory',

    ];
    
    public function register(): void
    {
        foreach($this->bindings as $key => $val)
        {
            $this->app->bind($key, $val);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
