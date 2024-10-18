<form action="{{ route('admin.order.index') }}" method="" style="padding-bottom: 20px">
    <div class="filter-wrapper">
        <div class="row">
            <div class="perpage col-sm-1">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @for ($i = 20; $i <= 200; $i += 20)
                            <option {{ $perpage == $i ? 'selected' : '' }} value="{{ $i }}">
                                {{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="perpage col-sm-2">
              <div class="date-box-item">
                <input type="text" name="created_at" value="" class="rangepicker form-control">
              </div>
            </div>
            <div class="action col-sm-9">
                <div class="row d-flex">
                    <div class="col-sm-8" style="display:flex;justify-content:space-between">
                        @foreach (__('cart') as $key => $val)
                            @php
                                ${$key} = request($key) ?: old($key);
                            @endphp
                            <select name="{{ $key }}" id class="form-control mr10 setupSelect2">
                                @foreach ($val as $index => $item) 
                                    <option {{ (${$key} == $index) ? 'selected' : ''}} value="{{ $index }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-10">
                                <input type="text" name="keyword" value="{{ request('keyword') ?: old('keyword') }}"
                                    placeholder="Nhập Từ khóa bạn muốn tìm kiếm ..." class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" name="search" value="search"
                                    class="btn btn-primary mb10 btn-sm"> Tìm kiếm</button>
                            </div>
                        </div>
                        {{-- <div class="col-sm-2">
                            <button type="submit" name="search" value="search"
                                class="btn btn-primary mb10 btn-sm"> Tìm kiếm</button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
