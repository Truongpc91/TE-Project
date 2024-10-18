<?php

namespace App\Providers;

use App\Http\ViewComposers\LanguageComposer;
use App\Http\ViewComposers\MenuComposer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Models\Language;
use App\Http\ViewComposers\SystemComposer;

use Carbon\Carbon;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    protected $serviceBindings = [
        'App\Services\Interfaces\UserServiceInterface' => 'App\Services\UserService',
        'App\Services\Interfaces\UserCatalogueServiceInterface' => 'App\Services\UserCatalogueService',
        'App\Services\Interfaces\PermissionServiceInterface' => 'App\Services\PermissionService',
        'App\Services\Interfaces\PostCatalogueServiceInterface' => 'App\Services\PostCatalogueService',
        'App\Services\Interfaces\LanguageServiceInterface' => 'App\Services\LanguageService',
        'App\Services\Interfaces\GenerateServiceInterface' => 'App\Services\GenerateService',
        'App\Services\Interfaces\PostServiceInterface' => 'App\Services\PostService',
        'App\Services\Interfaces\ProductCatalogueServiceInterface' => 'App\Services\ProductCatalogueService',
        'App\Services\Interfaces\ProductServiceInterface' => 'App\Services\ProductService',
        'App\Services\Interfaces\AttributeCatalogueServiceInterface' => 'App\Services\AttributeCatalogueService',
        'App\Services\Interfaces\AttributeServiceInterface' => 'App\Services\AttributeService',
        'App\Services\Interfaces\SystemServiceInterface' => 'App\Services\SystemService',
        'App\Services\Interfaces\MenuServiceInterface' => 'App\Services\MenuService',
        'App\Services\Interfaces\MenuCatalogueServiceInterface' => 'App\Services\MenuCatalogueService',
        'App\Services\Interfaces\SlideServiceInterface' => 'App\Services\SlideService',
        'App\Services\Interfaces\WidgetServiceInterface' => 'App\Services\WidgetService',
        'App\Services\Interfaces\PromotionServiceInterface' => 'App\Services\PromotionService',
        'App\Services\Interfaces\SourceServiceInterface' => 'App\Services\SourceService',
        'App\Services\Interfaces\CustomerCatalogueServiceInterface' => 'App\Services\CustomerCatalogueService',
        'App\Services\Interfaces\CustomerServiceInterface' => 'App\Services\CustomerService',
        'App\Services\Interfaces\CartServiceInterface' => 'App\Services\CartService',
        'App\Services\Interfaces\OrderServiceInterface' => 'App\Services\OrderService',
        'App\Services\Interfaces\ReviewServiceInterface' => 'App\Services\ReviewService',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->serviceBindings as $key => $value) {
            $this->app->bind($key, $value);
        }

        $this->app->register(ReponsitoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $locale = app()->getLocale(); // vn en cn
        $language = Language::where('canonical', $locale)->first();
        
        Validator::extend('custom_date_format', function($attribute, $value, $parameters, $validator){
            return DateTime::createFromFormat('d/m/Y H:i', $value) !== false;
        });

        Validator::extend('custom_after', function($attribute, $value, $parameters, $validator){
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $validator->getData()[$parameters[0]]);
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $value);
            
            return $endDate->greaterThan($startDate) !== false;
        });

        view()->composer('frontend.homepage.layout', function ($view) use ($language) {
            $composerClass = [
                SystemComposer::class,
                MenuComposer::class,
                LanguageComposer::class
            ];

            foreach ($composerClass as $key => $val) {
                $composer = app()->make($val, ['language' => $language->id]);
                $composer->compose($view);
            }
        });
        

        Schema::defaultStringLength(255);
    }
}
