@include('backend.dashboard.components.breadcrumb', [
    'title' => $config['seo'][$config['method']]['title'],
])
@include('backend.dashboard.components.formError')
@php
    $url = $config['method'] == 'create' ? route('admin.attribute_catalogue.store') : route('admin.attribute_catalogue.update', $attributeCatalogue);
@endphp
<form action="{{ $url }}" method="post" class="box" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.components.content', ['model' => $attributeCatalogue ?? null])
                    </div>
                </div>
                {{-- @include('backend.dashboard.components.album', ['model' => ($attributeCatalogue) ?? null]) --}}
                @include('backend.dashboard.components.seo', ['model' => $attributeCatalogue ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.attribute.catalogue.components.aside')
            </div>
        </div>
        @include('backend.dashboard.components.button')
    </div>
</form>
