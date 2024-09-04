<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên Ngôn ngữ</th>
            <th>Canonical</th>
            <th>Ảnh</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($languages) && is_object($languages))
            @foreach ($languages as $language)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $language->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span>{{ $language->name }}</span>
                    </td>
                    <td>
                        <span>{{ $language->canonical }}</span>
                    </td>
                    <td>
                        <span class="image img-cover">
                            <img src="{{ \Storage::url($language->image)}}"
                                alt="">
                        </span>
                    </td>
                    <td class="text-navy text-center js-switch-{{ $language->id }}">
                        <input type="checkbox" value="{{ $language->publish }}" class="js-switch status"
                            data-field="publish" data-model="Language"
                            {{ $language->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $language->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.language.edit', $language) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.language.delete', $language) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
