<div class="row">
    <div class="col-lg-5">
        <div class="ibox">
            <div class="ibox-content">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="false" class="collapsed">Liên kết tự tạo</a>
                            </h5>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false"
                            style="height: 0px;">
                            <div class="panel-body">
                                <div class="panel-title">Tạo Menu</div>
                                <div class="panel-description">
                                    <p>+ Cài đặt menu mà bạn muốn hiển thị</p>
                                    <p><small class="text-danger">* Khi khởi tạo menu bạn phải chắc chắn rằng đường dẫn
                                            của menu có hoạt động. Đường dẫn trên website được khởi tạo tại các module:
                                            Bài viết, Sản phẩm, dự án,...</small></p>
                                    <p><small>* Tiêu đề và đường dẫn của menu không được bỏ trống.</small></p>
                                    <p><small>* Hệ thống chri hỗ trợ tối đa 5 cấp menu.</small></p>
                                    <a href=""
                                        style="color:#000;border-color:#c4cdd5;display:inline-block !important;"
                                        title="" class="btn btn-default add-menu m-b m-r right">Thêm đường dẫn</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach (__('module.model') as $key => $val)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#{{ $key }}"
                                        class="collapsed menu-module" aria-expanded="false"
                                        data-model="{{ $key }}">{{ $val }}</a>
                                </h4>
                            </div>
                            <div id="{{ $key }}" class="panel-collapse collapse" aria-expanded="false"
                                style="height: 0px;">
                                <div class="panel-body">
                                    <form action="" method="GET" data-model="{{ $key }}"
                                        class="search-model">
                                        <div class="form-row">
                                            <input type="text" name="keyword" id=""
                                                class="form-control search-menu"
                                                placeholder="Nhập 2 ký tự để tìm kiếm ..." autocomplete="off">
                                        </div>
                                    </form>
                                    <div class="menu-list">

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="col-lg-4">
                    <label for="">Tên Menu</label>
                </div>
                <div class="col-lg-4">
                    <label for="">Đường dẫn</label>
                </div>
                <div class="col-lg-2">
                    <label for="">Vị trí Menu</label>
                </div>
                <div class="col-lg-2">
                    <label for="">Xóa</label>
                </div>
                <hr class="hr-line-dashed" style="padding-top:20px">
                @php
                    $menu = old('menu', $menuList ?? null);
                @endphp
                {{-- @dd(is_array($menu)) --}}
                <div class="menu-wrapper text-center ">
                    <div class="notification {{ (is_array($menu) && count($menu)) ? 'none' : '' }}">
                        <h4 style="font-weight:500; font-size:16px;color:#000">Danh sách liên kết này chưa có bất kí
                            đường dẫn nào.</h4>
                        <p style="color:#555;margin-top:10px;">Hãy nhấn vào <span style="color:blue;">"Thêm đường
                                dẫn"</span> để bắt đầu thêm.</p>
                    </div>
                    @if (is_array($menu) && count($menu))
                        @foreach ($menu['name'] as $key => $val)
                            <div class="row mb10 menu-item {{ $menu['canonical'][$key] }}">
                                <div class="col-lg-4"><input type="text" value="{{ $val }}"
                                        class="form-control" name="menu[name][]"></div>
                                <div class="col-lg-4"><input type="text" value="{{ $menu['canonical'][$key] }}"
                                        class="form-control" name="menu[canonical][]"></div>
                                <div class="col-lg-2"><input type="text" value="{{ $menu['order'][$key] }}"
                                        class="form-control int text-right" name="menu[order][]">
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-row text-center"><a class="delete-menu"><i
                                                class="fa fa-trash"></i></a></div>
                                </div>
                                <input type="text" class="hidden" name="menu[id][]" value="{{ $menu['id'][$key] }}">
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
