<form action="{{ route('admin.{view}.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.components.filterPublish')
                    @include('backend.dashboard.components.keyword')
                    <a href="{{ route('admin.{view}.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>{{ __('messages.{module}.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>

