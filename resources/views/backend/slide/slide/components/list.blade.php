<div class="ibox">
    <div class="ibox-title">
        <div style="display:flex;justify-content: space-between;">
            <h5>Danh sách Slides</h5>
            <button type="button" class="addSlide btn">Thêm Slide</button>
        </div>
    </div>
    @php
        $slides = old('slide', $slideItem ?? []);
        $i = 1;
        // dd($slides)
    @endphp
    <div class="ibox-content">
        <div class="row slide-list">
            <div class="text-danger slide-notification {{ count($slides) > 0 ? 'hidden' : '' }}">Chưa có hình ảnh nào
                được chọn ...</div>

            @if ($slides)
                @foreach ($slides['description'] as $key => $val)
                    @php
                        $image = ($slides['image'][$key]) ?? '';
                        $description = $val;
                        $canonical = $slides['canonical'][$key];
                        $name = $slides['name'][$key];
                        $alt = $slides['alt'][$key];
                        $window = isset($slides['window'][$key]) ? $slides['window'][$key] : '';
                    @endphp
                    <div class="col-lg-12 ui-state-default">
                        <div class="slide-item">
                            <div class="row custom-row">
                                <div class="col-lg-3 mb-10">
                                    <label for="file-upload {{ $key }}" class="slide-image img-cover">
                                        @if ($image)
                                            <img src="{{ \Storage::url($image) }}" alt="">
                                        @else
                                            <svg style="width:100px;height:100px;fill: #d3dbe2;margin-bottom: 10px;"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80">
                                                <path
                                                    d="M80 57.6l-4-18.7v-23.9c0-1.1-.9-2-2-2h-3.5l-1.1-5.4c-.3-1.1-1.4-1.8-2.4-1.6l-32.6 7h-27.4c-1.1 0-2 .9-2 2v4.3l-3.4.7c-1.1.2-1.8 1.3-1.5 2.4l5 23.4v20.2c0 1.1.9 2 2 2h2.7l.9 4.4c.2.9 1 1.6 2 1.6h.4l27.9-6h33c1.1 0 2-.9 2-2v-5.5l2.4-.5c1.1-.2 1.8-1.3 1.6-2.4zm-75-21.5l-3-14.1 3-.6v14.7zm62.4-28.1l1.1 5h-24.5l23.4-5zm-54.8 64l-.8-4h19.6l-18.8 4zm37.7-6h-43.3v-51h67v51h-23.7zm25.7-7.5v-9.9l2 9.4-2 .5zm-52-21.5c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zm-13-10v43h59v-43h-59zm57 2v24.1l-12.8-12.8c-3-3-7.9-3-11 0l-13.3 13.2-.1-.1c-1.1-1.1-2.5-1.7-4.1-1.7-1.5 0-3 .6-4.1 1.7l-9.6 9.8v-34.2h55zm-55 39v-2l11.1-11.2c1.4-1.4 3.9-1.4 5.3 0l9.7 9.7c-5.2 1.3-9 2.4-9.4 2.5l-3.7 1h-13zm55 0h-34.2c7.1-2 23.2-5.9 33-5.9l1.2-.1v6zm-1.3-7.9c-7.2 0-17.4 2-25.3 3.9l-9.1-9.1 13.3-13.3c2.2-2.2 5.9-2.2 8.1 0l14.3 14.3v4.1l-1.3.1z">
                                                </path>
                                            </svg>
                                        @endif
                                    </label>
                                    <input id="file-upload {{ $key }}" type="file" style="display:none"
                                        name="slide[image][]" />
                                    <div class="small-text">Sử dụng nút chọn hình để thêm mới hình ảnh</div>
                                    <span class="btn btn-danger deleteSlide"><i class="fa fa-trash"></i></span>
                                </div>
                                <div class="col-lg-9">
                                    <div class="tabs-container">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" href="#tab_{{ $key }}"
                                                    aria-expanded="true">
                                                    Thông
                                                    tin chung</a></li>
                                            <li class=""><a data-toggle="tab" href="#tab_{{ $key + 1 }}"
                                                    aria-expanded="false">SEO</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="tab_{{ $key }}" class="tab-pane active">
                                                <div class="panel-body">
                                                    <div class="label-text">
                                                        Mô tả
                                                    </div>
                                                    <div class="form-row mb10">
                                                        <textarea name="slide[description][]" id="" class="form-control">{{ $description }}</textarea>
                                                    </div>
                                                    <div class="form-row form-row-url">
                                                        <input type="text" name="slide[canonical][]" class="form-control"
                                                            placeholder="URL" value="{{ $canonical }}">
                                                        <div class="overlay">
                                                            <div class="uk-flex uk-flex-middle">
                                                                <label for="input_{{ $key }}">Mở trong tab
                                                                    mới</label>
                                                                <input type="checkbox" name="slide[window][]"
                                                                    value="_blank"
                                                                    {{ $window == '_blank' ? 'checked' : '' }}
                                                                    class="" id="input_{{ $key }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="tab_{{ $key + 1 }}" class="tab-pane">
                                                <div class="panel-body">
                                                    <div class="form-row form-row-url slide-seo-tab">
                                                        <div class="label-text">
                                                            Tiêu đề ảnh
                                                        </div>
                                                        <input type="text" name="slide[name][]" class="form-control"
                                                            placeholder="Tiêu đề ảnh" value="{{ $name }}">
                                                        <div class="label-text">
                                                            Mô tả ảnh
                                                        </div>
                                                        <input type="text" name="slide[alt][]" class="form-control"
                                                            placeholder="Mô tả ảnh" value="{{ $alt }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    @php
                        $i += 2;
                    @endphp
                @endforeach
            @endif
        </div>
    </div>
</div>
