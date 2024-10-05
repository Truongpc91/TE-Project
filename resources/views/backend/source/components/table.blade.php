<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Nguồn khách hàng</th>
            <th>Từ khóa</th>
            <th>Mô tả</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($sources) && is_object($sources))
            @foreach ($sources as $source)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $source->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $source->name }}
                    </td>
                    <td>
                        {{ $source->keyword }}
                    </td>
                    <td>
                        {{ strip_tags(html_entity_decode($source->description)) }}
                    </td>
                    <td class="text-navy text-center js-switch-{{ $source->id }}">
                        <input type="checkbox" value="{{ $source->publish }}" class="js-switch status"
                            data-field="publish" data-model="User" {{ $source->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $source->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.source.edit', $source) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.source.delete', $source) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{-- {{ $sources->links('pagination::bootstrap-4') }} --}}
