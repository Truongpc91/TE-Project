@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')

@php
    $url = $config['method'] == 'create' ? route('admin.widget.store') : route('admin.widget.udpate', $widget->id);
@endphp

<form action="{{ $url }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin Widget</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.components.content', [
                            'offTitle' => true,
                            'offContent' => true,
                            'model' => ($widget) ?? ''
                        ])
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung Widget</h5>
                    </div>
                    <div class="ibox-content model-list">
                        <div class="labelText">Chọn Module</div>
                        @foreach (__('module.model') as $key => $val)
                            <div class="model-item" style="display:flex;">
                                <input type="radio" id="{{ $key }}" class="input-radio"
                                    value="{{ $key }}" name="model"
                                    {{ old('model', ($widget->model) ?? null) == $key ? 'checked' : '' }}>
                                <label for="{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                        <div class="search-model-box">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control search-model">
                            <div class="ajax-search-result"></div>
                        </div>
                        @php
                            $modelItem = old('modelItem', $widgetItem ?? null);
                        @endphp
                        <div class="search-model-result">
                            @if (!is_null($modelItem))
                                @foreach ($modelItem['id'] as $key => $val)
                                    <div class="search-result-item" id="model-{{ $val }}" data-model-id="{{ $val }}">
                                        <div class="" style="display:flex;justify-content:space-between">
                                            <div class="" style="display:flex;">
                                                <span class="image img-cover">
                                                    <img src="http://shopprojectt.test//storage/{{$modelItem['image'][$key] }}"
                                                        alt=""> 
                                                </span>
                                                <span class="name">{{ $modelItem['name'][$key] }}</span>
                                                <div class="hidden">
                                                    <input type="text" name="modelItem[id][]" value="{{ $val }}">
                                                    <input type="text" name="modelItem[name][]" value="{{$modelItem['name'][$key] }}">
                                                    <input type="text" name="modelItem[image][]"
                                                        value="{{$modelItem['image'][$key] }}">
                                                </div>
                                            </div>
                                            <div class="deleted btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.widget.widget.components.aside')
            </div>
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
