@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.components.formError')
@php
    $url = ($config['method'] == 'create') ? route('admin.{view}.store') : route('admin.{view}.update', ${module}->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.components.content', ['model' => (${module}) ?? null])
                    </div>
                </div>
               @include('backend.dashboard.components.album', ['model' => (${module}) ?? null])
               @include('backend.dashboard.components.seo', ['model' => (${module}) ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.{view}.components.aside')
            </div>
        </div>
        @include('backend.dashboard.components.button')
    </div>
</form>
