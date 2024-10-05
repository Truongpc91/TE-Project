<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\SourceServiceInterface as SourceService;
use App\Repositories\Interfaces\SourceReponsitoryInterface as SourceReponsitory;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;

use App\Http\Requests\Source\StoreSourceRequest;
use App\Http\Requests\Source\UpdateSourceRequest;
use App\Models\Language;

class SourceController extends Controller
{
    protected $SourceService;
    protected $SourceReponsitory;
    protected $LanguageReponsitory;

    public function __construct(
        SourceService $SourceService,
        SourceReponsitory $SourceReponsitory,
        LanguageReponsitory $LanguageReponsitory
    ) {
        $this->SourceService = $SourceService;
        $this->SourceReponsitory = $SourceReponsitory;
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
        $this->authorize('modules', 'admin.sources.index');
        $sources = $this->SourceService->paginate($request, $this->language);
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

        $config['seo'] = __('messages.source');

        $template = 'backend.source.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'sources'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.sources.create');
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/source.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.source');
        $config['method'] = 'create';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSourceRequest $request)
    {
        if ($this->SourceService->create($request)) {
            return redirect()->route('admin.source.index')->with('success', 'Thêm mới Nguồn khách thành công !');
        } else {
            return redirect()->route('admin.source.index')->with('error', 'Thêm mới Nguồn khách thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        $this->authorize('modules', 'admin.sources.update');

        $source = $this->SourceReponsitory->findById($id);
       
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/finder.js',
                'backend/library/source.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = __('messages.source');
        $config['method'] = 'edit';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source',
        ));
    }

    public function update (UpdateSourceRequest $request, $id)
    {
        if ($this->SourceService->update($request, $id)) {
            return redirect()->route('admin.source.index')->with('success', 'Cập nhật Nguồn khách thành công !');
        } else {
            return redirect()->route('admin.source.index')->with('error', 'Cập nhật Nguồn khách thất bại! Hãy thử lại');
        }
    }

    public function delete($id)
    {
        $this->authorize('modules', 'admin.sources.destroy');
        $source = $this->SourceReponsitory->findById($id);
        $template = 'backend.source.delete';
        $config['seo'] = __('messages.source');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source'
        ));
    }

    public function destroy($id)
    {
        if ($this->SourceService->destroy($id)) {
            return redirect()->route('admin.source.index')->with('success', 'Xóa Nguồn khách thành công !');
        } else {
            return redirect()->route('admin.source.index')->with('error', 'Xóa Nguồn khách thất bại! Hãy thử lại');
        }
    }

    public function saveTranslate(Request $request){
        if ($this->SourceService->saveTranslate($request, $this->language)) {
            return redirect()->route('admin.source.index')->with('success', 'Dịch Nguồn khách thành công !');
        } else {
            return redirect()->route('admin.source.index')->with('error', 'Dịch Nguồn khách thất bại! Hãy thử lại');
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
