<div class="action col-sm-{{ isset($model) ? '4' : '6' }}">
    <div class="row d-flex">
        <div class="col-sm-8 ">
            <div class="row">
                <div class="col-sm-10">
                    <input 
                        type="text" 
                        name="keyword" 
                        value="{{ request('keyword') ?: old('keyword') }}"
                        placeholder="Nhập Từ khóa bạn muốn tìm kiếm ..." 
                        class="form-control"
                    >
                </div>
                <div class="col-sm-2">
                    <button type="submit" name="search" value="search"
                        class="btn btn-primary mb10 btn-sm"> Tìm kiếm</button>
                </div>
            </div>
        </div>
    </div>
</div>