<div class="col-lg-4">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Thời gian áp dụng chương trình</h5>
        </div>
        {{-- @php
            $startDate = convertDateTime($model->startDate);
            $endDate = convertDateTime($model->endDate);
            dd($startDate, $endDate);
        @endphp --}}
        <div class="ibox-content">
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày bắt đầu <span
                        class="text-danger">(*)</span></label>
                <input type="text" name="startDate" value="{{ old('startDate', (isset($model) ? (convertDateTime($model->startDate) ?? '') : '')) }}"
                    class="form-control datepicker" autocomplete="">
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày kết thúc</label>
                <input type="text" name="endDate" value="{{ old('endDate',(isset($model) ? (convertDateTime($model->endDate) ?? '') : '')) }}"
                    class="form-control datepicker" autocomplete="" @if (old('neverEndDate', $pronotion->neverEndDate ?? null) == 'accept') dissable @endif>
            </div>
            <div class="form-row">
                <div class="" style="display:flex;justify-content:space-between">
                    <label for="neverEnd" class="fix-label">Không có ngày kết thúc</label>
                    <input type="checkbox" name="neverEndDate" value="accept" class="" id="neverEnd"
                        @if (old('neverEndDate', $pronotion->neverEndDate ?? null) == 'accept') checked="checked" @endif>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-title">
            <h5>Nguồn khách áp dụng</h5>
        </div>
        @php
            $sourceStatus = old('source', ($model->discountInformation['source']['status']) ?? null);
        @endphp
        <div class="ibox-content">
            <div class="row">
                <div class="setting-value">
                    <input type="radio" id="allSource" class="chooseSource" name="source" value="all"
                        checked=""
                        {{ old('source', ($model->discountInformation['source']['status'] ?? '')) == 'all' || !old('source') ? 'checked' : '' }}>
                    <label for="allSource" class="fix-label">Áp dụng cho toàn bộ nguồn khách</label>
                </div>
                <div class="setting-value">
                    <input type="radio" id="chooseSource" class="chooseSource" name="source" value="choose"
                        {{ old('source', ($model->discountInformation['source']['status'] ?? '')) == 'choose' ? 'checked' : '' }}>
                    <label for="chooseSource" class="fix-label">Chọn nguồn khách áp dụng</label>
                </div>
            </div>
            @if ($sourceStatus)
                @php
                    $sourceValue = old('sourceValue', $model->discountInformation['source']['data'] ?? []);
                @endphp
                <div class="source-wrapper">
                    <select name="sourceValue[]" id="" class="multipleSelect2" multiple>
                        @foreach ($sources as $key => $val)
                            <option value="{{ $val->id }}"
                                {{ in_array($val->id, $sourceValue) ? 'selected' : '' }}>
                                {{ $val->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-title">
            <h5>Đối tượng áp dụng</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="setting-value">
                    <input type="radio" id="allApply" class="chooseApply" name="applyStatus" value="all"
                        {{ old('applyStatus', ($model->discountInformation['apply']['status']) ?? '') == 'all' || !old('applyStatus') ? 'checked' : '' }}>
                    <label for="allApply" class="fix-label">Áp dụng cho toàn bộ đối tượng</label>
                </div>
                <div class="setting-value">
                    <input type="radio" id="chooseApply" class="chooseApply" name="applyStatus" value="choose"
                        {{ old('applyStatus', ($model->discountInformation['apply']['status']) ?? '') == 'choose' ? 'checked' : '' }}>
                    <label for="chooseApply" class="fix-label">Chọn đối tượng khách hàng</label>
                </div>
            </div>
            @php
                $applyStatus = old('applyStatus', ($model->discountInformation['apply']['status'])  ?? null);
                $applyValue = old('applyValue', ($model->discountInformation['apply']['data']) ?? []);
                // dd($applyStatus, $applyValue);
            @endphp
            @if ($applyStatus)
                <div class="apply-wrapper">
                    <select name="applyValue[]" id="" class="multipleSelect2 conditionItem" multiple>
                        @foreach (__('module.applyStatus') as $key => $val)
                            <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wrapper-condition">

                </div>
            @endif
        </div>
    </div>
</div>

<input type="hidden" class="input-product-and-quantity" value="{{ json_encode(__('module.item')) }}">
<input type="hidden" class="applyStatusList" value="{{ json_encode(__('module.applyStatus')) }}">
<input type="hidden" class="conditionItemSelected"
    value="{{ json_encode(old('applyValue', $applyValue ?? [])) }}">
   
@if (count($applyValue))
    @foreach ($applyValue as $key => $val)
        <input type="hidden" class="condition_input_{{ $val }}" value="{{ json_encode(old($val, ($model->discountInformation['apply']['condition'][$val])) ?? null) }}">
    @endforeach
@endif
