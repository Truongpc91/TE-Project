<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width:50px;">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>{{ __('messages.tableName') }}</th>
            @include('backend.dashboard.components.languageTh')
            <th style="width:80px;" class="text-center">{{ __('messages.tableOrder') }}</th>
            <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }}</th>
            <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }}</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($attributes) && is_object($attributes))
            @foreach ($attributes as $attribute)
                <tr id="{{ $attribute->id }}">
                    <td>
                        <input type="checkbox" value="{{ $attribute->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="">
                            {{-- <div class="">
                            <img src="{{ \Storage::url($attribute->image) }}" alt="" width="80">
                        </div> --}}
                            <div class="main-info">
                                <div class="name"><span class="maintitle"
                                        style="color:#2962ff;font-weight: 700;">{{ $attribute->name }}</span></div>
                                <div class="catalogue">
                                    <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                    @foreach ($attribute->attribute_catalogues as $val)
                                        @foreach ($val->attribute_catalogue_language as $cat)
                                            <a href="{{ route('admin.attribute.index', ['attribute_catalogue_id' => $val->id]) }}"
                                                title="">{{ $cat->name }}</a>
                                        @endforeach
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.components.languageTd', [
                        'model' => $attribute,
                        'modeling' => 'Attribute',
                    ])
                    <td>
                        <input type="text" name="order" value="{{ $attribute->order }}"
                            class="form-control sort-order text-right" data-id="{{ $attribute->id }}"
                            data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-center js-switch-{{ $attribute->id }}">
                        <input type="checkbox" value="{{ $attribute->publish }}" class="js-switch status "
                            data-field="publish" data-model="{{ $config['model'] }}"
                            {{ $attribute->publish == 2 ? 'checked' : '' }} data-modelId="{{ $attribute->id }}" />
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.attribute.edit', $attribute) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.attribute.delete', $attribute) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $attributes->links('pagination::bootstrap-4') }}
