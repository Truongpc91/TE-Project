<head>
    <base href="{{ config('app.url') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="">
    <meta name="author" content="">
    <meta name="copyright" content="">
    <meta name="description" content="">
    <meta name="canonical" content="">
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('frontend/resources/img/logo.jpg') }}" type="" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('frontend/resources/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/resources/uikit/css/uikit.modify.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend/resources/library/css/library.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/resources/plugins/wow/css/libs/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/core/plugins/jquery-nice-select-1.1.0/css/nice-select.css') }}">
    <link href="../frontend/resources/style.css" rel="stylesheet">
    <link href="../backend/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="../frontend/core/css/product.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css">
    @if (isset($config['css']))
        @foreach ($config['css'] as $key => $val)
            <link href="{{ $val }}" rel="stylesheet">
        @endforeach
    @endif
    <title>TE + | Ecommerce</title>
</head>
