@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
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
            ? route('admin.posts.store')
            : route('admin.posts.udpate', $post);
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
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.post.post.components.general')
                    </div>
                </div>
                @include('backend.post.post.components.seo')
            </div>
            <div class="col-lg-3">
                @include('backend.post.post.components.aside')
            </div>
        </div>
        <div class="text-right mb15 button-fix">
            <button type="submit" class="btn btn-primary" name="send" value="send">{{ __('messages.save') }}</button>
        </div>
    </div>
</form>
