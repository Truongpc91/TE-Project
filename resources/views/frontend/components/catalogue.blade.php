<div class="category-children">
    <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
        @foreach ($category as $key => $val)
            @php
                $name = $val->languages->first()->pivot->name;
                $canonical = write_url($val->languages->first()->pivot->canonical);
            @endphp
            <li class=""><a href="{{ $canonical }}" title="">{{ $name }}</a></li>
        @endforeach
    </ul>
</div>
