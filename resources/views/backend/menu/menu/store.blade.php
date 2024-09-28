@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])

@php
    // $url = $config['method'] == 'create' ? route('admin.menu.store') : route('admin.users.udpate', $menu);
@endphp
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.menu.store') }}" method="POST" class="box menuContainer" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        @include('backend.menu.menu.components.catalogue')
        <hr>
        @include('backend.menu.menu.components.list')
        <input type="hidden" name="redirect" value="{{ ($id) ?? 0 }}">
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

@include('backend.menu.menu.components.popup')
