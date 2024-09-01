
<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Avarta</th>
            <th>Thông tin thành viên</th>
            <th>Địa chỉ</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($users) && is_object($users))
            @foreach ($users as $user)
                <tr >
                    <td>
                        <input type="checkbox" value="{{ $user->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <span class="image img-cover">
                            <img src="{{ \Storage::url($user->image)}}"
                                alt="">
                        </span>
                    </td>
                    <td>
                        <div class="infor-item name"><strong>Họ tên :</strong> {{ $user->name }}</div>
                        <div class="infor-item email"><strong>Email :</strong> {{ $user->email }}</div>
                        <div class="infor-item phone"><strong>Phone :</strong> {{ $user->phone }}</div>
                    </td>
                    <td>
                        <div class="address-item name"><strong>Địa chỉ :</strong> xxx</div>
                        <div class="address-item email"><strong>Xã :</strong> {{ $user->address }}</div>
                        <div class="address-item phone"><strong>Huyện :</strong> {{ $user->address }}</div>
                        <div class="address-item phone"><strong>Thành phố :</strong> {{ $user->address }} </div>

                    </td>
                    <td class="text-navy text-center js-switch-{{ $user->id }}">
                        <input 
                            type="checkbox" 
                            value="{{ $user->publish }}" 
                            class="js-switch status"
                            data-field="publish"
                            data-model="User"
                            {{ ($user->publish == '1') ? 'checked' : ''}} 
                            data-modelId="{{ $user->id }}"
                        />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.users.delete',$user) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
       
    </tbody>
</table>

{{ $users->links('pagination::bootstrap-4') }}
