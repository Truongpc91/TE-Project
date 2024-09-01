<!DOCTYPE html>
<html>

<head>
    @include('backend.dashboard.components.head')
</head>

<body>
    <div id="wrapper">
        @include('backend.dashboard.components.sidebar')

        <div id="page-wrapper" class="gray-bg">
            @include('backend.dashboard.components.navbar')
            @include($template)
            @include('backend.dashboard.components.footer')
        </div>
        @include('backend.dashboard.components.rightsidebar')
    </div>
    @include('backend.dashboard.components.script')
</body>

</html>
