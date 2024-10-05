<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideResponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Models\Language;

class SlideController extends Controller
{
    const PATH_UPLOAD = 'slides';

    protected $SlideService;
    protected $SlideResponsitory;
    protected $language;

    public function __construct(SlideService $SlideService, SlideResponsitory $SlideResponsitory)
    {
        $this->SlideService = $SlideService;
        $this->SlideResponsitory = $SlideResponsitory;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.slides.index');
        $slides = $this->SlideService->paginate($request);

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

        $config['seo'] = __('messages.slide');

        $template = 'backend.slide.slide.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'slides'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.slides.create');


        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/library/slide.js'
            ]
        ];

        $config['seo'] = __('messages.slide');
        $config['method'] = 'create';
        $template = 'backend.slide.slide.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSlideRequest $request)
    {
        // if ($request->hasFile('image')) {
        //     $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        // }

        if ($this->SlideService->create($request, $this->language)) {
            return redirect()->route('admin.slide.index')->with('success', 'Thêm mới Slide thành công !');
        } else {
            return redirect()->route('admin.slide.index')->with('error', 'Thêm mới Slide thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        $this->authorize('modules', 'admin.slides.update');
        $slide = $this->SlideResponsitory->findById($id);

        $slideItem = $this->SlideService->convertSlideArray($slide->item[$this->language]);
        // dd($slideItem);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/library/slide.js'
            ]
        ];

        $config['seo'] = __('messages.slide');
        $config['method'] = 'edit';
        $template = 'backend.slide.slide.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide',
            'slideItem'
        ));
    }

    public function update(UpdateSlideRequest $request, $id)
    {
        // $slide = $this->SlideResponsitory->findById($id);

        // if ($request->hasFile('image')) {
        //     $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        // }

        // $currentImage = $slide->image;

        // if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
        //     Storage::delete($currentImage);
        // }

       

        if ($this->SlideService->update($request, $id, $this->language)) {
            return redirect()->route('admin.slide.index')->with('success', 'Cập nhật Slide thành công !');
        } else {
            return redirect()->route('admin.slide.index')->with('error', 'Cập nhật Slide thất bại! Hãy thử lại');
        }
    }

    public function delete(Slide $slide)
    {
        $this->authorize('modules', 'admin.slides.destroy');

        $template = 'backend.slide.slide.delete';
        $config['seo'] = __('messages.slide');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide'
        ));
    }

    public function destroy(Slide $slide)
    {
        $currentImage = $slide->image;

        if ($currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        if ($this->SlideService->destroy($slide)) {
            return redirect()->route('admin.slides.index')->with('success', 'Xóa Slide thành công !');
        } else {
            return redirect()->route('admin.slides.index')->with('error', 'Xóa Slide thất bại! Hãy thử lại');
        }
    }
}
