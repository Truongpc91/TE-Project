@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')

<form action="{{ route('admin.language.storeTranslate') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="option[id]" value="{{ $option['id'] }}">
    <input type="hidden" name="option[languageId]" value="{{ $option['languageId'] }}">
    <input type="hidden" name="option[model]" value="{{ $option['model'] }}">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.components.content', [
                            'model' => $object ?? null,
                            'disabled' => 1,
                        ])
                    </div>
                    @include('backend.dashboard.components.seo', [
                        'model' => $object ?? null,
                        'disabled' => 1,
                    ])
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox-title">
                    <h5>{{ __('messages.tableHeading') }}</h5>
                </div>
                <div class="ibox-content">
                    @include('backend.dashboard.components.translate', [
                        'model' => $objectTranslate ?? null,
                        'disabled' => 0,
                    ])
                </div>
                @include('backend.dashboard.components.seoTranslate', [
                    'model' => $objectTranslate ?? null,
                    'disabled' => 0,
                ])
            </div>
        </div>
        <div class="text-right mb15 button-fix">
            <button type="submit" class="btn btn-primary" name="send"
                value="send">{{ __('messages.save') }}</button>
        </div>
    </div>
</form>
