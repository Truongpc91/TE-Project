<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên chương trình</th>
            <th>Chiết khấu</th>
            <th>Thông tin</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            @include('backend.dashboard.components.languageTh')
            <th>Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($promotions) && is_object($promotions))
            @foreach ($promotions as $promotion)
            @php
                $status = '';
                if($promotion->endDate != null && strtotime($promotion->endDate) - strtotime(now()) <= 0){
                    $status = '<span class="text-danger">- Hết hạn</span>';
                }
            @endphp
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $promotion->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <div class="" style="display:flex"> 
                            {{ $promotion->name }} {!! $status !!}
                        </div>
                        <div class="label label-primary">Mã KM : {{ $promotion->code }}</div> 
                    </td>
                    <td>
                        <div class="discount-information text-danger">
                            {!! renderDiscountInformation($promotion)  !!}
                        </div>
                    </td>
                    <td>
                        <div class="">{{ __('module.promotion')[$promotion->method] }}</div>
                    </td>
                    <td>
                        {{ $promotion->startDate }}
                    </td>
                    <td>
                        {{ ($promotion->endDate === 'accept') ? 'Không giới hạn' : $promotion->endDate}}
                    </td>
                    {{-- @foreach ($languages as $language)
                    @php
                       $translated = (isset($promotion->description[$language->id])) ? 1 : 0;   
                    @endphp
                        @if (session('app_locale') === $language->canonical)
                            @continue
                        @endif
                        <td class="text-center">
                            <a class="{{ ($translated == 1) ? '' : 'text-danger'}}"
                                href="{{ route('admin.promotion.translate', ['languageId' => $language->id, 'id' => $promotion->id]) }}">{{ ($translated == 1) ? 'Đã dịch' : 'Chưa dịch'}}</a>
                        </td>
                    @endforeach --}}
                    <td class="text-navy text-center js-switch-{{ $promotion->id }}">
                        <input type="checkbox" value="{{ $promotion->publish }}" class="js-switch status"
                            data-field="publish" data-model="User" {{ $promotion->publish == 1 ? 'checked' : '' }}
                            data-modelId="{{ $promotion->id }}" />
                    </td>
                    <td class="text-navy text-center">
                        <a href="{{ route('admin.promotion.edit', $promotion) }}" class="btn btn-success"><i
                                class="fa fa-edit"></i></a>
                        <a href="{{ route('admin.promotion.delete', $promotion) }}" class="btn btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{-- {{ $promotions->links('pagination::bootstrap-4') }} --}}
