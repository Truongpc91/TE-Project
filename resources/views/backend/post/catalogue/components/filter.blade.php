<form action="{{ route('admin.post_catalogue.index') }}" method="">
    <div class="filter-wrapper">
        <div class="row">
            <div class="perpage col-sm-2">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @for ($i = 20; $i <= 200; $i += 20)
                            <option {{ ($perpage == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }} {{ __('messages.perpage') }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action col-sm-8">
                <div class="row d-flex">
                    <div class="col-sm-4">
                        @php
                            $publish = request('publish') ?: old('publish');
                        @endphp
                        <select name="publish" id="" class="form-control mr10 mb10 setupSelect2">
                            @foreach (__('messages.publish') as $key => $val)
                                <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-10">
                                <input 
                                    type="text" 
                                    name="keyword" 
                                    value="{{ request('keyword') ?: old('keyword') }}"
                                    placeholder="{{ __('messages.searchInput') }}" 
                                    class="form-control"
                                >
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" name="search" value="search"
                                    class="btn btn-primary mb10 btn-sm"> {{ __('messages.search') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('admin.post_catalogue.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i> {{ __('messages.addnew') }}</a>
            </div>
        </div>
    </div>
</form>
