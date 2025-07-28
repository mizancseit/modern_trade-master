@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Change Password
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Change Password
                            </small>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="alert alert-danger" id="errorMsg" style="display: none;">
              <strong>Warning!</strong> <span id="errorMsgShow"></span>
            </div>

            <div class="alert alert-success" id="successMsg" style="display: none;">
              <strong>Successfully!</strong> Your password change successfully. Your session has been end.
            </div>
            
            <!-- Body Start-->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Change Password Info</h2>                            
                        </div>
                        <div class="body">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="text" name="userName" class="form-control" value="{{ Auth::user()->email }}" required="" disabled="">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="password" name="oldPassword" id="oldPassword" class="form-control" value="" required="" placeholder="Old Password" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="password" name="newPassword" id="newPassword" class="form-control" value="" required=""  placeholder="New Password" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" value="" required="" placeholder="Confirm Password" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <p></p>
                            <div class="row" style="text-align: center;">
                                <div class="col-sm-2">
                                    <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="changePassword()">Change Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Body End-->
        </div>
    </section>
@endsection