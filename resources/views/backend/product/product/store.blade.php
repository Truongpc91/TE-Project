@include('backend.dashboard.components.breadcrumb', [
    'title' => $config['seo'][$config['method']]['title'],
])
@include('backend.dashboard.components.formError')
@php
    $url = $config['method'] == 'create' ? route('admin.product.store') : route('admin.product.udpate', $product);
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
                        @include('backend.dashboard.components.content', ['model' => $product ?? null])
                    </div>
                </div>
                @include('backend.product.product.components.variant')
                @include('backend.dashboard.components.seo', ['model' => $product ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.product.product.components.aside', ['details' => true])
            </div>
        </div>
        @include('backend.dashboard.components.button')
    </div>
</form>
