@include('backend.dashboard.components.breadcrumb', [
    'title' => $config['seo']['create']['children'] . $menu->languages->first()->pivot->name,
])

@php
    $url =
        $config['method'] == 'create'
            ? route('admin.menu.store')
            : ($config['method'] == 'children'
                ? route('admin.menu.saveChildren', $menu->id)
                : route('admin.users.udpate', $menu));
@endphp
@include('backend.dashboard.components.formError')
<form action="{{ $url }}" method="POST" class="box menuContainer" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <hr>
        @include('backend.menu.menu.components.list')
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

@include('backend.menu.menu.components.popup')
