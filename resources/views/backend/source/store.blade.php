@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')

@php
    $url = $config['method'] == 'create' ? route('admin.source.store') : route('admin.source.udpate', $source->id);
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
                        <h5>Thông tin nguồn khách hàng</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.components.content', [
                            'offTitle' => true,
                            'offContent' => true,
                            'model' => ($source) ?? ''
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.source.components.aside')
            </div>
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
