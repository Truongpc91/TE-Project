<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên</th>
            <th>Từ khóa</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($menuCatalogues) && is_object($menuCatalogues))
            @foreach ($menuCatalogues as $menuCatalogue)
                <tr >
                    <td>
                        <input type="checkbox" value="{{ $menuCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                       {{ $menuCatalogue->name }}
                    </td>
                    <td>
                        {{ $menuCatalogue->keyword }}
                    </td>
                   
                    <td class="text-navy text-center js-switch-{{ $menuCatalogue->id }}">
                        <input 
                            type="checkbox" 
                            value="{{ $menuCatalogue->publish }}" 
                            class="js-switch status"
                            data-field="publish"
                            data-model="Menu"
                            {{ ($menuCatalogue->publish == 1) ? 'checked' : ''}} 
                            data-modelId="{{ $menuCatalogue->id }}"
                        />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.menu.edit', $menuCatalogue) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.menu.delete', $menuCatalogue) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
       
    </tbody>
</table>

