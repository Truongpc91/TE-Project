<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Avarta</th>
            <th>Thông tin thành viên</th>
            <th>Địa chỉ</th>
            <th>Nhóm thành viên</th>
            <th>Nguồn khách hàng</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($customers) && is_object($customers))
            @foreach ($customers as $customer)
                <tr >
                    <td>
                        <input type="checkbox" value="{{ $customer->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span class="image img-cover">
                            <img src="{{ \Storage::url($customer->image)}}"
                                alt="">
                        </span>
                    </td>
                    <td>
                        <div class="infor-item name"><strong>Họ tên :</strong> {{ $customer->name }}</div>
                        <div class="infor-item email"><strong>Email :</strong> {{ $customer->email }}</div>
                        <div class="infor-item phone"><strong>Phone :</strong> {{ $customer->phone }}</div>
                    </td>
                    <td>
                        <div class="address-item name"><strong>Địa chỉ :</strong> xxx</div>
                        <div class="address-item email"><strong>Xã :</strong> {{ $customer->address }}</div>
                        <div class="address-item phone"><strong>Huyện :</strong> {{ $customer->address }}</div>
                        <div class="address-item phone"><strong>Thành phố :</strong> {{ $customer->address }} </div>
                    </td>
                    <td>
                        {{ $customer->customer_catalogues->name }}
                    </td>
                    <td>
                        {{ $customer->sources->name }}
                    </td>
                    <td class="text-navy text-center js-switch-{{ $customer->id }}">
                        <input 
                            type="checkbox" 
                            value="{{ $customer->publish }}" 
                            class="js-switch status"
                            data-field="publish"
                            data-model="Customer"
                            {{ ($customer->publish == 1) ? 'checked' : ''}} 
                            data-modelId="{{ $customer->id }}"
                        />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.customer.edit',$customer) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.customer.delete',$customer) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
       
    </tbody>
</table>

{{ $customers->links('pagination::bootstrap-4') }}
