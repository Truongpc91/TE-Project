<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>
@php
    $coreScript = [
        'backend/js/plugins/toastr/toastr.min.js',
        'frontend/resources/plugins/wow/dist/wow.min.js',
        'frontend/resources/uikit/js/uikit.min.js',
        'frontend/resources/uikit/js/components/sticky.min.js',
        'frontend/resources/library/js/jquery.js',
        'frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js',
        'frontend/resources/function.js',
    ];

    if (isset($config['js'])) {
        foreach ($config['js'] as $key => $val) {
            array_push($coreScript, $val);
        }
    }
@endphp
@foreach ($coreScript as $key => $item)
    <script src="{{ asset($item) }}"></script>
@endforeach

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- UIkit JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.15.18/js/uikit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.15.18/js/uikit-icons.min.js"></script>

<script src="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js
"></script>

<script async defer crossorigin="anonymous"
    src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0&appId=103609027035330&autoLogAppEvents=1"
    nonce="E1aWx0Pa"></script>
