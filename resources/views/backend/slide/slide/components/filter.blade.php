<form action="{{ route('admin.slide.index') }}" method="">
    <div class="filter-wrapper">
        <div class="row">
            @include('backend.dashboard.components.filterPublish')
            @include('backend.dashboard.components.keyword')
            <div class="col-sm-2">
                <a href="{{ route('admin.slide.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i> Thêm
                    mới</a>
            </div>
        </div>
    </div>
</form>
