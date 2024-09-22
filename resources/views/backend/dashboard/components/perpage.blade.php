<div class="perpage col-sm-2">
    @php
        $perpage = request('perpage') ?: old('perpage');
    @endphp
    <div class="">
        <select name="perpage" class="form-control input-sm perpage filter mr10">
            @for ($i = 20; $i <= 200; $i += 20)
                <option {{ ($perpage == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }} bản ghi</option>
            @endfor
        </select>
    </div>
</div>