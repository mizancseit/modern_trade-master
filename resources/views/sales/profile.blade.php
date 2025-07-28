@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Your Profile
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Profile
                            </small>
                        </h2>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Profile Info</h2>                            
                        </div>
                        <div class="body">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">person</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="text" name="fullName" class="form-control" value="{{ Auth::user()->businessOwnerName }}" placeholder="Your Full Name" required="" maxlength="50">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">business</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="text" name="businessName" class="form-control" value="{{ Auth::user()->businessName }}" placeholder="Business Name" required="" maxlength="50">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                </span>
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" placeholder="Business Email" required="" maxlength="45">
                                    </div>
                                </div>
                            </div>

                            
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">phone</i>
                                </span>
                                <div class="col-md-2 align-left" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="text" class="form-control" value="+880" disabled="">
                                    </div>
                                </div>

                                <div class="col-md-10" style="padding-left:0px;">
                                    <div class="form-line">
                                        <input type="number" class="form-control" name="businessNumber" minlength="10" maxlength="10" placeholder="Business Phone" value="{{ Auth::user()->businessPhone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">room</i>
                                </span>
                                
                                <div class="col-md-12" style="padding-left:0px;">
                                    <div class="form-line">
                                        <textarea class="form-control" placeholder="Business Address"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group" style="padding-bottom: 0px;">
                                <span class="input-group-addon">
                                    <i class="material-icons">account_circle</i>
                                </span>
                                
                                <div class="col-md-6" style="padding-left:0px; padding-bottom: 0px; padding-top: 30px">
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="businessPhoto" onchange="loadFile(event)">
                                    </div>
                                </div>

                                <span class="input-group-addon">
                                    <i class="material-icons">account_circle</i>
                                </span>
                                
                                <div class="col-md-6" style="padding-left:0px; padding-bottom: 0px; padding-top: 30px">
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="businessPhoto" onchange="loadFile1(event)">
                                    </div>
                                </div>

                            </div>

                            <div class="input-group">
                                
                                <div class="col-md-6" style="padding-top:0px;">
                                    
                                    <div>
                                        <img src="{{URL::asset('resources/sales/images/default-business.png')}}" width="150" height="150" id="output">
                                    </div>
                                    <label>Your Photo</label>
                                </div>

                                

                                <div class="col-md-6" style="padding-top:0px;">
                                   
                                    <div>
                                        <img src="{{URL::asset('resources/sales/images/default-business.png')}}" width="150" height="150" id="output1">
                                    </div>
                                     <label>Business Logo</label>
                                </div>

                            </div>



                            
                            


                            <p></p>
                            <div class="row" style="text-align: center;">
                                <div class="col-sm-2">
                                    <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Update</button>
                                </div>
                            </div>

                                
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection