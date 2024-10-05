@php
    $title = str_replace('{language}', $language['name'], $config['seo']['index']['translate']).' '.$widget['name'];
@endphp

@include('backend.dashboard.components.breadcrumb', ['title' => $title])
@include('backend.dashboard.components.formError')

<form action="{{ route('admin.widget.saveTranslate') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="translateId" value="{{ $language->id }}">
    <input type="hidden" name="widgetId" value="{{ $widget->id }}">
    <input type="hidden" name="">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.components.content', [
                            'model' => $widget ?? null,
                            'disabled' => 1,
                            'offTitle' => true,
                            'offContent' => true
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>{{ __('messages.tableHeading') }}</h5>
                </div>
                <div class="ibox-content">
                    @include('backend.dashboard.components.translate', [
                        'model' => $widgetTranslate ?? null,
                        'disabled' => 0,
                        'offTitle' => true,
                        'offTitle' => true,
                        'offContent' => true,
                    ])
                </div>
            </div>
        </div>
        <div class="text-right mb15 button-fix">
            <button type="submit" class="btn btn-primary" name="send"
                value="send">{{ __('messages.save') }}</button>
        </div>
    </div>
</form>
