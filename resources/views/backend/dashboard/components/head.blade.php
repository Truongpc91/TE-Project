<base href="{{ env('APP_URL') }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf_token" content="{{ csrf_token() }}">

<title>T-Ecommerce + | Dashboard v.2</title>
<!-- Latest compiled and minified CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
{{-- <link href="backend/css/bootstrap.min.css" rel="stylesheet">
<link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">

<link href="backend/css/animate.css" rel="stylesheet">
<link href="backend/css/style.css" rel="stylesheet">
<link href="backend/css/customize.css" rel="stylesheet">

<link rel="icon" href="backend/img/logo.jpg" type="image/x-icon" /> --}}

<link href="../backend/css/bootstrap.min.css" rel="stylesheet">
<link href="../backend/font-awesome/css/font-awesome.css" rel="stylesheet">

<link href="../backend/css/animate.css" rel="stylesheet">
<link href="../backend/css/style.css" rel="stylesheet">
<link href="../backend/css/customize.css" rel="stylesheet">
<link href="../backend/css/customize.css" rel="stylesheet">
<link rel="icon" href="../backend/img/logo.jpg" type="image/x-icon" />
<link href="../backend/css/plugins/toastr/toastr.min.css" rel="stylesheet">
@if (isset($config['css']))
    @foreach ($config['css'] as $css => $value)
        <link href="{{ $value }}" rel="stylesheet">
    @endforeach
@endif

<script src="../backend/js/jquery-3.1.1.min.js"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

<script>
    var BASE_URL = '{{ env('APP_URL') }}';
    var SUFFIX  = '{{ config('apps.general.suffix') }}';
</script>
