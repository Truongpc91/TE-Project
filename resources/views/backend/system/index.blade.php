@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
{{-- @dd($system) --}}
<form action="" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        @foreach ($system as $key => $val)
            <div class="row">
                <div class="col-lg-5">
                    <div class="panel-head">
                        <div class="panel-title">{{ $val['label'] }}</div>
                        <div class="panel-description">
                            <p><span class="text-danger">(*)</span>{{ $val['description'] }}</p>
                            {{-- <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    @if (count($val['value']))
                        <div class="ibox">
                            <div class="ibox-content">
                                @foreach ($val['value'] as $keyVal => $item)
                                    @php
                                        $name = $key . '_' . $keyVal;
                                    @endphp
                                    <div class="row mb15">
                                        <div class="col-lg-12">
                                            <div class="form-row">
                                                <label for="" class="control-label text-right"
                                                    style="display:flex; justify-content: space-between;">
                                                    <span>{{ $item['label'] }}</span>
                                                    <span>{!! renderSystemLink($item) !!} </span>
                                                </label>
                                                @switch($item['type'])
                                                    @case('text')
                                                        <input type="{{ $item['type'] }}" class="form-control"
                                                            name="{{ $name }}" placeholder="" autocomplete="off"
                                                            value="{{ old($name) }}">
                                                    @break

                                                    @case('file')
                                                        <input type="{{ $item['type'] }}" class="form-control"
                                                            name="{{ $name }}" placeholder="" autocomplete="off"
                                                            value="{{ old($name) }}">
                                                    @break

                                                    @case('textarea')
                                                        <textarea class="form-control" name="{{ $name }}" placeholder="" a utocomplete="off"
                                                            value="{{ old($name) }}" style="height: 100px"></textarea>
                                                    @break
                                                        
                                                    @case('select')
                                                        {!! renderSystemSelect($item, $name) !!}    
                                                    @break
                                                @endswitch
                                                {{-- @if ($item['type'] == 'text' || $item['type'] == 'image')
                                                    <input type="{{ $item['type'] }}" class="form-control"
                                                        name="{{ $name }}" placeholder="" autocomplete="off"
                                                        value="{{ old($name) }}">
                                                @else
                                                    <textarea class="form-control"
                                                        name="{{ $name }}" 
                                                        placeholder="" a
                                                        utocomplete="off"
                                                        value="{{ old($name) }}"
                                                        style="height: 100px"></textarea>
                                                @endif --}}

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
