<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Services\Interfaces\PromotionServiceInterface as PromotionService;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;
use App\Repositories\Interfaces\SourceReponsitoryInterface as SourceReponsitory;

use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;
use App\Models\Language;

class PromotionController extends Controller
{
    protected $PromotionService;
    protected $PromotionReponsitory;
    protected $LanguageReponsitory;
    protected $SourceReponsitory;

    public function __construct(
        PromotionService $PromotionService,
        PromotionReponsitory $PromotionReponsitory,
        LanguageReponsitory $LanguageReponsitory,
        SourceReponsitory $SourceReponsitory

    ) {
        $this->PromotionService = $PromotionService;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->LanguageReponsitory = $LanguageReponsitory;
        $this->SourceReponsitory = $SourceReponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.promotion.index');
        $promotions = $this->PromotionService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];

        $config['seo'] = __('messages.promotion');

        $template = 'backend.promotion.promotion.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'promotions'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.promotion.create');
        $sources = $this->SourceReponsitory->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/css/plugins/datapicker/datepicker3.css',
                'backend/css/plugins/datapicker/jquery.datetimepicker.min.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/library.js',
                'backend/library/promotion.js',
                'backend/plugins/datetime.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.promotion');
        $config['method'] = 'create';
        $template = 'backend.promotion.promotion.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources'
        ));
    }

    public function store(StorePromotionRequest $request)
    {
        if ($this->PromotionService->create($request, $this->language)) {
            return redirect()->route('admin.promotion.index')->with('success', 'Thêm mới Promotion thành công !');
        } else {
            return redirect()->route('admin.promotion.index')->with('error', 'Thêm mới Promotion thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        $this->authorize('modules', 'admin.promotion.update');

        $promotion = $this->PromotionReponsitory->findById($id);
        $sources = $this->SourceReponsitory->all();

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/promotion.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.promotion');
        $config['method'] = 'edit';
        $template = 'backend.promotion.promotion.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'sources'
        ));
    }

    public function update (UpdatePromotionRequest $request, $id)
    {
        if ($this->PromotionService->update($request, $id, $this->language)) {
            return redirect()->route('admin.promotion.index')->with('success', 'Cập nhật Promotion thành công !');
        } else {
            return redirect()->route('admin.promotion.index')->with('error', 'Cập nhật Promotion thất bại! Hãy thử lại');
        }
    }

    public function delete($id)
    {
        $this->authorize('modules', 'admin.promotion.destroy');
        $promotion = $this->PromotionReponsitory->findById($id);
        $template = 'backend.promotion.promotion.delete';
        $config['seo'] = __('messages.promotion');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion'
        ));
    }

    public function destroy($id)
    {
        if ($this->PromotionService->destroy($id)) {
            return redirect()->route('admin.promotion.index')->with('success', 'Xóa Promotion thành công !');
        } else {
            return redirect()->route('admin.promotion.index')->with('error', 'Xóa Promotion thất bại! Hãy thử lại');
        }
    }

    public function translate($widgetId,$languageId)
    {
        $this->authorize('modules', 'admin.promotion.translate');
        $promotion = $this->PromotionReponsitory->findById($widgetId);
        $language = $this->LanguageReponsitory->findById($languageId);
        $promotion->jsonDescription = $promotion->description;
        $promotion->description = $promotion->description[$this->language];

        $widgetTranslate = new \stdClass;
        $widgetTranslate->description = ($promotion->jsonDescription[$languageId]) ?? null;

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/promotion.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.promotion');
        $config['method'] = 'create';
        $template = 'backend.promotion.promotion.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'language',
            'widgetTranslate'
        ));
    }

    public function saveTranslate(Request $request){
        if ($this->PromotionService->saveTranslate($request, $this->language)) {
            return redirect()->route('admin.promotion.index')->with('success', 'Dịch Promotion thành công !');
        } else {
            return redirect()->route('admin.promotion.index')->with('error', 'Dịch Promotion thất bại! Hãy thử lại');
        }
    }

    private function menuItemArgument(array $whereIn = [], $languageId)
    {
        return [
            'condition' => [],
            'flag' => true,
            'relation' => [
                'languages' => function ($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ],
            'orderBy' => ['id', 'DESC'],
            'param' => [
                'whereIn' => $whereIn,
                'whereInField' => 'id'
            ]
        ];
    }
}
