<form action="{{ route('admin.{module}.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.components.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.components.filterPublish')
                    @php
                        ${module}CatalogueId = request('{module}_catalogue_id') ?: old('{module}_catalogue_id');
                    @endphp
                    <select name="{module}_catalogue_id" class="form-control setupSelect2 ml10">
                        @foreach($dropdown as $key => $val)
                        <option {{ (${module}CatalogueId == $key)  ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.components.keyword')
                    <a href="{{ route('admin.{module}.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>{{ config('apps.{module}.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>

