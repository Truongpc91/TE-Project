<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">{{ __('messages.parent') }}
                        <span class="text-danger">(*)</span>
                    </label><br>
                    <span class="text-danger font-italic notice"> {{ __('messages.parentNotice') }}</span>
                    <select name="parent_id" id="" class="form-control setupSelect2">
                        @foreach ($dropdown as $key => $value)
                            <option
                                {{ $key == old('parent_id', (isset($postCatalogue->parent_id)) ? $postCatalogue->parent_id : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    <input type="file" name="image" id=""
                        value="{{ old('image', $postCatalogue->image ?? '') }}">
                    @if (isset($postCatalogue))
                        <div class="mt-2" style="margin-top: 20px">
                            <img src="{{ \Storage::url($postCatalogue->image) }}" alt="" width="250">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    <div class="mb-1" style="margin-bottom: 20px">
                        <select name="publish" id="" class="form-control setupSelect2 mb20 mb-1">
                            @foreach (__('messages.publish') as $key => $value)
                                <option
                                    {{ $key == old('publish', isset($postCatalogue->publish) ? $postCatalogue->publish : '') ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" id="" class="form-control setupSelect2">
                        @foreach (__('messages.follow') as $key => $value)
                            <option
                                {{ $key == old('follow', isset($postCatalogue->follow) ? $postCatalogue->follow : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
