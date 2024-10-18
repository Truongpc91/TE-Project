<!DOCTYPE html>
<html lang="en">
@include('frontend.components.head')

<body>
    @include('frontend.components.header')
    @yield('content')
    @include('frontend.components.footer')
    @include('frontend.components.script')
    @include('frontend.components.popup')
</body>

</html>
