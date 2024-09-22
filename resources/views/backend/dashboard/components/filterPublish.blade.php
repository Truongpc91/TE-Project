{{-- @dd($model) --}}

<div class="col-sm-{{ isset($model) ? '2' : '4' }}">
    @php
        $publish = request('publish') ?: old('publish');
    @endphp
    <select name="publish" id="" class="form-control mr10 mb10 setupSelect2">
        @foreach (config('apps.general.publish') as $key => $val)
            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
        @endforeach
    </select>
</div>