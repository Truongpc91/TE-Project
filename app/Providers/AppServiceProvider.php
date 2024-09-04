<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $serviceBindings = [
        'App\Services\Interfaces\UserServiceInterface' => 'App\Services\UserService',
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',

        'App\Services\Interfaces\UserCatalogueServiceInterface' => 'App\Services\UserCatalogueService',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' => 'App\Repositories\UserCatalogueRepository',

        'App\Services\Interfaces\PostCatalogueServiceInterface' => 'App\Services\PostCatalogueService',
        'App\Repositories\Interfaces\PostCatalogueReponsitoryInterface' => 'App\Repositories\PostCatalogueReponsitory',
        
        'App\Services\Interfaces\LanguageServiceInterface' => 'App\Services\LanguageService',
        'App\Repositories\Interfaces\LanguageReponsitoryInterface' => 'App\Repositories\LanguageReponsitory',

        'App\Repositories\Interfaces\ProvinceReponsitoryInterface' => 'App\Repositories\ProvinceReponsitory',
        'App\Repositories\Interfaces\DistrictReponsitoryInterface' => 'App\Repositories\DistrictReponsitory'
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->serviceBindings as $key => $value) {
            $this->app->bind($key, $value);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
