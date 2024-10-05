<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetReponsitory;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;

use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;
use App\Models\Language;

class WidgetController extends Controller
{
    protected $WidgetService;
    protected $WidgetReponsitory;
    protected $LanguageReponsitory;

    public function __construct(
        WidgetService $WidgetService,
        WidgetReponsitory $WidgetReponsitory,
        LanguageReponsitory $LanguageReponsitory
    ) {
        $this->WidgetService = $WidgetService;
        $this->WidgetReponsitory = $WidgetReponsitory;
        $this->LanguageReponsitory = $LanguageReponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.widgets.index');
        $widgets = $this->WidgetService->paginate($request);
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

        $config['seo'] = __('messages.widget');

        $template = 'backend.widget.widget.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'widgets'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.widgets.create');
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreWidgetRequest $request)
    {
        if ($this->WidgetService->create($request, $this->language)) {
            return redirect()->route('admin.widget.index')->with('success', 'Thêm mới Widget thành công !');
        } else {
            return redirect()->route('admin.widget.index')->with('error', 'Thêm mới Widget thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        $this->authorize('modules', 'admin.widgets.update');

        $widget = $this->WidgetReponsitory->findById($id);
        $widget->description = $widget->description[$this->language];

        $modelClass = loadClass($widget->model);
        $widgetItem = convertArrayByKey($modelClass->findByCondition(
            ...array_values($this->menuItemArgument($widget->model_id, $this->language))
        ), ['id','name.languages', 'image']);
        // dd($widget);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.widget');
        $config['method'] = 'edit';
        $template = 'backend.widget.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'widgetItem'
        ));
    }

    public function update (UpdateWidgetRequest $request, $id)
    {
        if ($this->WidgetService->update($request, $id, $this->language)) {
            return redirect()->route('admin.widget.index')->with('success', 'Cập nhật Widget thành công !');
        } else {
            return redirect()->route('admin.widget.index')->with('error', 'Cập nhật Widget thất bại! Hãy thử lại');
        }
    }

    public function delete($id)
    {
        $this->authorize('modules', 'admin.widgets.destroy');
        $widget = $this->WidgetReponsitory->findById($id);
        $template = 'backend.widget.widget.delete';
        $config['seo'] = __('messages.widget');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget'
        ));
    }

    public function destroy($id)
    {
        if ($this->WidgetService->destroy($id)) {
            return redirect()->route('admin.widget.index')->with('success', 'Xóa Widget thành công !');
        } else {
            return redirect()->route('admin.widget.index')->with('error', 'Xóa Widget thất bại! Hãy thử lại');
        }
    }

    public function translate($widgetId,$languageId)
    {
        $this->authorize('modules', 'admin.widgets.translate');
        $widget = $this->WidgetReponsitory->findById($widgetId);
        $language = $this->LanguageReponsitory->findById($languageId);
        $widget->jsonDescription = $widget->description;
        $widget->description = $widget->description[$this->language];

        $widgetTranslate = new \stdClass;
        $widgetTranslate->description = ($widget->jsonDescription[$languageId]) ?? null;

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.widget.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'language',
            'widgetTranslate'
        ));
    }

    public function saveTranslate(Request $request){
        if ($this->WidgetService->saveTranslate($request, $this->language)) {
            return redirect()->route('admin.widget.index')->with('success', 'Dịch Widget thành công !');
        } else {
            return redirect()->route('admin.widget.index')->with('error', 'Dịch Widget thất bại! Hãy thử lại');
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
