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
        @if (isset($customer_catalogues) && is_object($customer_catalogues))
            @foreach ($customer_catalogues as $customer_catalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $customer_catalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span>{{ $customer_catalogue->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-success">{{ $customer_catalogue->customers_count }} </span> người
                    </td>
                    <td>
                        <span>{{ $customer_catalogue->description }}</span>
                    </td>
                    <td class="text-navy text-center js-switch-{{ $customer_catalogue->id }}">
                        <input type="checkbox" value="{{ $customer_catalogue->publish }}" class="js-switch status"
                            data-field="publish" data-model="UserCatalogue"
                            {{ $customer_catalogue->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $customer_catalogue->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.customer_catalogue.edit', $customer_catalogue) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.customer_catalogue.delete', $customer_catalogue) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
