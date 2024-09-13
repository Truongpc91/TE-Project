@foreach($languages as $language)
    @if(session('app_locale') === $language->canonical) 
        @continue
    @endif
<td class="text-center">

    @php
        $translated = $model->languages->contains('id', $language->id);
    @endphp

    <a 
        class="{{ ($translated) ? '' : 'text-danger' }}"
        href="{{ route('admin.language.translate', ['id' => $model->id, 'languageId' => $language->id, 'model' => $modeling]) }}">{{ ($translated) ? 'Đã dịch'  : 'Chưa dịch' }}</a>
    {{-- <a href="{{ route('admin.language.translate', ['id' => $post_catalogue->id, 'languageId' => $language->id, 'model' => 'PostCatalogue']) }}">Chưa dịch</a> --}}
    </td>
@endforeach