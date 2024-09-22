<form action="{{ route('admin.attribute.index') }}" style="padding-bottom: 20px">
    <div class="filter-wrapper">
        <div class="row">
            @include('backend.dashboard.components.perpage')
            <div class="action">
                <div class="row d-flex">
                    @include('backend.dashboard.components.filterPublish',['model' => 'Product'])
                    <div class="col-sm-2">
                        @php
                            $attributeCatalogueId = request('attribute_catalogue_id') ?: old('attribute_catalogue_id');
                        @endphp
                        <select name="attribute_catalogue_id" class="form-control setupSelect2 ml10">
                            @foreach ($dropdown as $key => $val)
                                <option {{ $attributeCatalogueId == $key ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('backend.dashboard.components.keyword',['model' => 'Product'])
                    {{-- <a href="{{ route('admin.product.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>{{ config('apps.product.create.title') }}</a> --}}
                    <div class="col-sm-1">
                        <a href="{{ route('admin.attribute.create') }}" class="btn btn-danger"><i
                                class="fa fa-plus mr5"></i>{{ __('messages.attribute.create.title') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
