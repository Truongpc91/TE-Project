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
        @if (isset($post_catalogues) && is_object($post_catalogues))
            @foreach ($post_catalogues as $post_catalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $post_catalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span>{{ $post_catalogue->name }}</span>
                    </td>
                    <td>
                        <span>{{ $post_catalogue->canonical }}</span>
                    </td>
                    <td>
                        <span class="image img-cover">
                            <img src="{{ \Storage::url($post_catalogue->image)}}"
                                alt="">
                        </span>
                    </td>
                    <td class="text-navy text-center js-switch-{{ $post_catalogue->id }}">
                        <input type="checkbox" value="{{ $post_catalogue->publish }}" class="js-switch status"
                            data-field="publish" data-model="PostCatalogue"
                            {{ $post_catalogue->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $post_catalogue->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.post_catalogue.edit', $post_catalogue) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.post_catalogue.delete', $post_catalogue) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
