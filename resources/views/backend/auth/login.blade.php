<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>T-Ecommerce + | Login</title>

    <link href="backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="backend/css/animate.css" rel="stylesheet">
    <link href="backend/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">
    <div class="loginColumns animated fadeInDown">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <h1 class="logo-name">TE+</h1>
                </div>
                <h3>Welcome to T-Ecommerce +</h3>
                <p>E-commerce site developed by Truonqpc91.
                    <!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
                </p>
                <p>Login in. To see it in action.</p>
            </div>
            <div class="col-md-6">
                <div class="inbox-content">
                    <form class="m-t" role="form" action="{{ route('auth.login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" placeholder="Email" name="email" value="{{ request('email') ?: old('email') }}">
                            @error('email')
                                <span class="text-danger font-italic">* {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" placeholder="Password" name="password">
                            @error('password')
                                <span class="text-danger font-italic">* {{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                        <a href="#"><small>Forgot password?</small></a>
                        <p class="text-muted text-center"><small>Do not have an account?</small></p>
                        <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a>
                    </form>
                </div>
            </div>
            <div class="text-center">
                <p class="m-t"> <small>Truonqpc91 &copy; 2004</small> </p>
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
