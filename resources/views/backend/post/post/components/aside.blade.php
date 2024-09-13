<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">{{ __('messages.parent') }}
                        <span class="text-danger">(*)</span>
                    </label><br>
                    <span class="text-danger font-italic notice"> {{ __('messages.parentNotice') }}
                        cha</span>
                    <select name="post_catalogue_id" id="" class="form-control setupSelect2">
                        @foreach ($dropdown as $key => $value)
                            <option
                                {{ $key == old('post_catalogue_id', (isset($post->post_catalogue_id)) ? $post->post_catalogue_id : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($post)){
                foreach ($post->post_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }

        @endphp
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Danh mục phụ
                        <span class="text-danger">(*)</span>
                    </label><br>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option 
                            @if(is_array(old('catalogue', (
                                isset($catalogue) && count($catalogue)) ?   $catalogue : [])
                                ) && isset($post->post_catalogue_id) && $key !== $post->post_catalogue_id &&  in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : []))
                            )
                            selected
                            @endif value="{{ $key }}">{{ $val }}</option>
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
                    {{-- <span class="image image-cover image-target">
                        <img src="backend/img/no-image.png" alt="" width="200">
                    </span>
                    <input type="hidden" name="image" value=""> --}}
                    <input type="file" name="image" id=""
                        value="{{ old('image', $post->image ?? '') }}">
                    @if (isset($post))
                        <div class="mt-2" style="margin-top: 20px">
                            <img src="{{ \Storage::url($post->image) }}" alt="" width="250">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="ibox">
    <div class="ibox-title">
        <h5>Album Ảnh</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    <input type="file" name="album[]" id=""
                        value="{{ old('album', $post->album ?? '') }}">
                    @if (isset($album))
                        <div class="mt-2" style="margin-top: 20px">
                            <img src="{{ \Storage::url($album->image) }}" alt="" width="250">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> --}}
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
                                    {{ $key == old('publish', isset($post->publish) ? $post->publish : '') ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" id="" class="form-control setupSelect2">
                        @foreach (__('messages.follow') as $key => $value)
                            <option
                                {{ $key == old('follow', isset($post->follow) ? $post->follow : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
