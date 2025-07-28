{{-- 
/**
*
* Created by Md. Sharifur Rahman
* Date : 
*
**/ 
--}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title> ABC | SSG | Login </title>

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
            <a href="javascript:void(0);"><b>ABC</b></a>
            {{-- <small>Admin BootStrap Based - Material Design</small> --}}
        </div>

        <!-- for message show -->
        @if(Session::has('msg'))
            <div class="alert alert-danger">
            {{ Session::get('msg') }}                        
            </div>
        @endif

        @if(Session::has('success'))
            <div class="alert alert-success">
            {{ Session::get('success') }}                        
            </div>
        @endif

        <div class="card">
            <div class="body">
                <form id="sign_in" action="{{ URL('/login') }}" method="post">
                    {{ csrf_field() }}    <!-- token -->  

                    <div class="form-group" style="text-align: center;">
                        <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="userEmail" placeholder="User Name" value="{{ old('userEmail') }}" autocomplete="off" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="userPassword" placeholder="Password" value="{{ old('userPassword') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 p-t-5">
                            <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Remember Me</label>
                        </div>
                        <div class="col-xs-4">
                                <button class="btn btn-block bg-pink waves-effect" type="submit">SIGN IN</button>
                        </div>
                    </div>

                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6"></div>
                        <div class="col-xs-6"></div>
                    </div>
                    <!-- <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="{{ URL('/register') }}">Register Now!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="{{ URL('/forgot-password') }}">Forgot Password?</a>
                        </div>
                    </div> -->
                </form>
            </div>
            
        </div>
        
    </div>

    <hr/>
    <div class="row">
        <div class="col-md-10 text-right" style="color: #FFF;">
            Copyright ABC © {{date('Y')}}
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
    <script src="{{URL::asset('resources/sales/js/pages/examples/sign-in.js')}}"></script>
</body>

</html>