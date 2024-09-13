<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tiêu đề</th>
            @include('backend.dashboard.components.languageTh')
            <th style="width:5%">Sắp xếp</th>    
            <th style="width:10%">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($posts) && is_object($posts))
            @foreach ($posts as $post)
                <tr id="{{ $post->id }}">
                    <td>
                        <input type="checkbox" value="{{ $post->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="">
                            <img src="{{ \Storage::url($post->image) }}" alt="" width="100">
                        </div>
                        <div class="main-info">
                            <span class="main-title">{{ $post->name }}</span>
                            <div class="catalogue">
                                <span class="text-danger">Nhóm hiển thị</span>
                                @foreach ($post->post_catalogues as $val)
                                    @foreach ($val->post_catalogue_language as $cat)
                                        <a href="">{{ $cat->name }} </a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </td>
                    @include('backend.dashboard.components.languageTd', ['model' => $post, 'modeling' => 'Post'])
                    <td>
                        <input type="text" name="order" value="{{ $post->order }}"
                            class="form-control sort-order text-right" data-id="{{ $post->id }}"
                            data-model="{{ $config['model'] }}">
                    </td>
                    <td class="text-navy text-center js-switch-{{ $post->id }}">
                        <input type="checkbox" value="{{ $post->publish }}" class="js-switch status"
                            data-field="publish" data-model="{{ $config['model'] }}"
                            {{ $post->publish == 1 ? 'checked' : '' }} data-modelId="{{ $post->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.posts.delete', $post) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>

{{ $posts->links() }}
