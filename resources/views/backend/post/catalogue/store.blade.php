@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url =
        $config['method'] == 'create'
            ? route('admin.post_catalogue.store')
            : route('admin.post_catalogue.udpate', $post_catalogue);
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
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.post.catalogue.components.general')
                    </div>
                </div>
                @include('backend.post.catalogue.components.seo')
            </div>
            <div class="col-lg-3">
                @include('backend.post.catalogue.components.aside')
            </div>
        </div>
        <div class="text-right mb15 button-fix">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
