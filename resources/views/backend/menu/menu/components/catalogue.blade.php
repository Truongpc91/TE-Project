<div class="row">
    <div class="col-lg-5">
        <div class="panel-head">
            <div class="panel-title">Vị trí Menu</div>
            <div class="panel-description">
                <p>+ Website có các vị trí hiển thị cho từng menu</p>
                <p>Lựa chọn vị trí mà bạn muốn hiên thị</p>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="" style="display:flex; justify-content:space-between">
                            <div class="" class="text-bold" style="font-weight:bold">Chọn vị trí hiển thị
                                <span class="text-danger">(*)</span>
                            </div>
                            <div class="">
                                <button data-toggle="modal" data-target="#createMenuCatalogue" type="button"
                                    name="" class="createMenuCatalogue btn btn-danger">
                                    Tạo vị trí hiển thị
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" style="padding-top:20px ">
                        @if (count($menuCatalogues))
                            <select class="setupSelect2" name="menu_catalogue_id" id="" style="width:100%">
                                <option value="0">[Chọn vị trí hiển thị]</option>
                                @foreach ($menuCatalogues as $key => $val)
                                    <option
                                        {{ isset($menuCatalogue) && $menuCatalogue->id == $val->id ? 'selected' : '' }}
                                        value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
