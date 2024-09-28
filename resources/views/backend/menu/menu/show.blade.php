@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['show']['title']])

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-4">
            <div class="" style="padding: 20px">
                @foreach ($languages as $language)
                    @php
                        $url =
                            session('app_locale') === $language->canonical
                                ? route('admin.menu.edit',$id)
                                : route('admin.menu.translate', ['languageId' => $language->id, 'id' => $id]);
                    @endphp
                    <td class="text-center">
                        <a class="" href="{{ $url }}">
                            <span style="padding: 20px"><img src="{{ \Storage::url($language->image) }}" alt=""
                                    width="30"></span>
                        </a>
                    </td>
                @endforeach
            </div>
            <div class="panel-title">Danh sách Menu</div>
            <div class="panel-description">
                <p>+ Danh sách Menu giúp bạn dễ dàng kiểm soát bố cục menu. Bạn có thể thêm mới menu bằng nút <span
                        class="text-success">Cập nhật Menu</span></p>
                <p>+ Bạn có thể thay đổi vị trí hiển thị của Menu bằng cách <span class="text-success"></span>menu đến
                    vị
                    trí mong muốn</p>
                <p>+ Dễ dàng tạo Menu con bằng cách nhấn vào nút <span class="text-success">Quản lý Menu con</span></p>
                <p><span class="text-danger">+ Hộ trợ tới danh mục con cấp 5</span></p>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between"
                        style="display:flex;justify-content:space-between">
                        <h5 style="margin:0">{{ $menuCatalogue->name }}</h5>
                        <a href="{{ route('admin.menu.editMenu', $id) }}" class="custom-bottom">Cập nhật Menu cấp 1</a>
                    </div>
                </div>
                <div class="ibox-content" id="dataCatalogue" data-catalogueId="{{ $id }}">
                    @php
                        $menus = recursive($menus);
                        $menuString = recursive_menu($menus);
                    @endphp
                    @if (count($menus))
                        <div class="dd" id="nestable2">
                            <ol class="dd-list">
                                {!! $menuString !!}
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
