<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên Nhóm</th>
            <th>Từ Khóa</th>
            <th>Danh sách Hình ảnh</th>
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($slides) && is_object($slides))
            @foreach ($slides as $slide)
                <tr >
                    <td>
                        <input type="checkbox" value="{{ $slide->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $slide->name }}
                    </td>
                    <td>
                        {{ $slide->keyword }}
                    </td>
                    <td>
                        
                    </td>
                    <td class="text-navy text-center js-switch-{{ $slide->id }}">
                        <input 
                            type="checkbox" 
                            value="{{ $slide->publish }}" 
                            class="js-switch status"
                            data-field="publish"
                            data-model="User"
                            {{ ($slide->publish == 1) ? 'checked' : ''}} 
                            data-modelId="{{ $slide->id }}"
                        />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.slide.edit',$slide) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.slide.delete',$slide) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
       
    </tbody>
</table>

{{-- {{ $slides->links('pagination::bootstrap-4') }} --}}
