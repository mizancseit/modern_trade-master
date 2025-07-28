<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title> Sales Automation | SSG | Login </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon-->
    <link rel="icon" href="{{URL::asset('resources/sales/images/favicon.ico')}}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{URL::asset('resources/sales/plugins/node-waves/waves.css')}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{URL::asset('resources/sales/plugins/animate-css/animate.css')}}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{URL::asset('resources/sales/css/style.css')}}" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo" style="text-shadow: -1px -1px 0px rgba(255,255,255,0.3), 2px 2px 0px rgba(0,0,0,0.8);">
            <a href="javascript:void(0);"><b>Sales Automation</b></a>
            {{-- <small>Register a new business membership</small> --}}
        </div>
        <div class="card">
            <div class="body">
                <form id="forgot_password" method="POST">
                    <div class="form-group" style="text-align: center;">
                        <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo">
                    </div>
                    <div class="msg">
                        Enter your email address that you used to register. We'll send you an email with your username and a
                        link to reset your password.
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">RESET MY PASSWORD</button>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="{{ URL('/') }}">Sign In!</a>
                    </div>
                </form>
            </div>
        </div>
        </div>
        
    </div>

    <hr/>
    <div class="row">
        <div class="col-md-10 text-right" style="color: #FFF;">
            Copyright Sales Automation © {{date('Y')}}
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery/jquery.min.js')}}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{URL::asset('resources/sales/plugins/bootstrap/js/bootstrap.js')}}"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{URL::asset('resources/sales/jplugins/node-waves/waves.js')}}"></script>

    <!-- Validation Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-validation/jquery.validate.js')}}"></script>

    <!-- Custom Js -->
    <script src="{{URL::asset('resources/sales/js/admin.js')}}"></script>
    <script src="{{URL::asset('resources/sales/js/pages/examples/sign-up.js')}}"></script>
</body>

</html>