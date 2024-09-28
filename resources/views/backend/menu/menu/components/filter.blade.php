<form action="{{ route('admin.menu.index') }}" method="">
    <div class="filter-wrapper">
        <div class="row">
            @include('backend.dashboard.components.perpage')
            <div class="action col-sm-8">
                <div class="row d-flex">
                       @include('backend.dashboard.components.filterPublish')
                        @include('backend.dashboard.components.keyword')
                </div>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('admin.menu.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i> Thêm
                    mới</a>
            </div>
        </div>
    </div>
</form>
