<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên nhóm</th>
            <th>Số thành viên</th>
            <th>Mô tả</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($user_catalogues) && is_object($user_catalogues))
            @foreach ($user_catalogues as $user_catalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $user_catalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span>{{ $user_catalogue->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-success">{{ $user_catalogue->users_count }} </span> người
                    </td>
                    <td>
                        <span>{{ $user_catalogue->description }}</span>
                    </td>
                    <td class="text-navy text-center js-switch-{{ $user_catalogue->id }}">
                        <input type="checkbox" value="{{ $user_catalogue->publish }}" class="js-switch status"
                            data-field="publish" data-model="UserCatalogue"
                            {{ $user_catalogue->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $user_catalogue->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.user_catalogue.edit', $user_catalogue) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.user_catalogue.delete', $user_catalogue) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
