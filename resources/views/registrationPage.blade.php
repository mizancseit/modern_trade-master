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
            <small>Register a new business membership</small>
        </div>

        <!-- for message show -->        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="body">
                <form id="sign_up" action="{{ URL('/register-done') }}" method="post">
                    {{ csrf_field() }}    <!-- token -->        

                    <div class="form-group" style="text-align: center;">
                        <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="fullName" placeholder="Full Name" required value="{{ old('fullName') }}" maxlength="50" autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">business</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="businessName" placeholder="Business Name" required value="{{ old('businessName') }}" maxlength="50" autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Business Email" value="{{ old('email') }}" maxlength="45" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="businessPassword" minlength="6" placeholder="Password" value="{{ old('businessPassword') }}" required>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">phone</i>
                        </span>
                        <div class="col-md-3 align-left" style="padding-left:0px;">
                            <div class="form-line">
                                <input type="text" class="form-control" value="+880" disabled="">
                            </div>
                        </div>

                        <div class="col-md-9" style="padding-left:0px;">
                            <div class="form-line">
                                <input type="number" class="form-control" name="businessNumber" minlength="10" maxlength="10" placeholder="Business Phone" value="{{ old('businessNumber') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="checkbox" checked="" name="businessTerms" value="0" id="terms" class="filled-in chk-col-pink">
                        <label for="terms">I read and agree to the <a href="javascript:void(0);">terms of usage</a>.</label>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">SIGN UP</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="{{ URL('/') }}">You already have a membership?</a>
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