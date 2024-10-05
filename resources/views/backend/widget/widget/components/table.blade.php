<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên Widget</th>
            <th>Từ khóa</th>
            <th>Short_Code</th>
            @include('backend.dashboard.components.languageTh')
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($widgets) && is_object($widgets))
            @foreach ($widgets as $widget)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $widget->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $widget->name }}
                    </td>
                    <td>
                        {{ $widget->keyword }}
                    </td>
                    <td>
                        {{ $widget->short_code }}
                    </td>
                    @foreach ($languages as $language)
                    @php
                       $translated = (isset($widget->description[$language->id])) ? 1 : 0;   
                    @endphp
                        @if (session('app_locale') === $language->canonical)
                            @continue
                        @endif
                        <td class="text-center">
                            <a class="{{ ($translated == 1) ? '' : 'text-danger'}}"
                                href="{{ route('admin.widget.translate', ['languageId' => $language->id, 'id' => $widget->id]) }}">{{ ($translated == 1) ? 'Đã dịch' : 'Chưa dịch'}}</a>
                        </td>
                    @endforeach
                    <td class="text-navy text-center js-switch-{{ $widget->id }}">
                        <input type="checkbox" value="{{ $widget->publish }}" class="js-switch status"
                            data-field="publish" data-model="User" {{ $widget->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $widget->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.widget.edit', $widget) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.widget.delete', $widget) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{-- {{ $widgets->links('pagination::bootstrap-4') }} --}}
