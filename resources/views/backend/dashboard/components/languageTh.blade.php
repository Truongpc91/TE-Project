@foreach($languages as $language)
@if(session('app_locale') === $language->canonical) 
    @continue
@endif
<th class="text-center"><span style="padding: 20px"><img src="{{ \Storage::url($language->image) }}" alt="" width="30"></span></th>
@endforeach