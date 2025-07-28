@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            RETAILE RMANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div>                    
                </div>                
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        <div class="header">
                            <h2>NEW RETAILER</h2>                            
                        </div>

                        <form action="{{ URL('/fo/admin-add') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                            <div class="body"> 

                                <div class="input-group">
                                    <div class="col-md-8 align-left" style="padding-left:0px;">
                                        <b>Name of the Retailer <span style="color: #FF0000;">*</span></b>
                                        <div class="form-line">
                                            <input type="text" id="retailerName" name="retailerName" class="form-control" value="" placeholder="Enter Retailer Name" required="" maxlength="50">
                                        </div>
                                    </div>                                    

                                    <div class="col-md-4 align-left" style="padding-left:0px;">
                                        <b>Sap Code</b>
                                        <div class="form-line">
                                            <input type="text" id="sap_code" name="sap_code" class="form-control" value="@if(sizeof($resultDis)>0){{$resultDis->sap_code}} @endif" readonly="" style="background: #E1E1E1;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-8 align-left" style="padding-left:0px;">
                                    <div class="input-group">
                                        <b>Owner Name <span style="color: #FF0000;">*</span></b>
                                        <div class="form-line">
                                            <input type="text" id="ownerName" name="ownerName" class="form-control" value="" placeholder="Enter Owner Name" required="" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-4 align-left">
                                    <div class="row clearfix">
                                        <b>Division <span style="color: #FF0000;">*</span></b>
                                        <select id="division" name="division" class="form-control show-tick" required="" >
                                            @if(sizeof($resultFoInfo)>0)
                                            <option value="{{$resultFoInfo->div_id}}">{{$resultFoInfo->div_name}}</option>
                                            @else
                                            <option value=""> Sorry , no division found</option>
                                            @endif                                            
                                        </select>
                                    </div>
                                </div> 


                                <div class="col-md-8 align-left" style="padding-left:0px;">
                                    <div class="input-group">
                                        <b>Address <span style="color: #FF0000;">*</span></b>
                                        <div class="form-line">
                                            <input type="text" id="address" name="address" class="form-control" value="" placeholder="Enter Address" required="" maxlength="200">
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-4 align-left">
                                    <div class="row clearfix">
                                        <b>Point <span style="color: #FF0000;">*</span></b>
                                        <select id="point" name="point" class="form-control show-tick" required="" >
                                            @if(sizeof($resultFoInfo)>0)
                                            <option value="{{$resultFoInfo->point_id}}">{{$resultFoInfo->point_name}}</option>
                                            @else
                                            <option value=""> Sorry , no point found</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-8 align-left">
                                        <b>Route <span style="color: #FF0000;">* 
                                        </span> </b>
                                        <select id="routes" name="routes" class="form-control show-tick" {{-- data-live-search="true" --}} required="" >
                                            {{-- <option value="">--Select Route--</option> --}}
                                            @foreach($resultRoute as $routes)
                                            @php
                                            if($routes->route_id==Request::segment(4))
                                            {
                                            @endphp
                                            <option value="{{ $routes->route_id }}" selected="">{{ $routes->rname }}</option>
                                            @php
                                            }
                                            @endphp
                                            @endforeach
                                        </select>
                                    </div>                                    

                                    <div class="col-md-4 align-left" style="padding-left:0px;">
                                        <b>Territory <!-- <span style="color: #FF0000;">*</span> --></b>
                                        <select id="territory" name="territory" class="form-control show-tick">
                                            
                                            @if(sizeof($resultTerritory)>0)
                                            <option value="{{$resultTerritory->id}}">{{$resultTerritory->name.' : '.$resultTerritory->id}}</option>
                                            @else
                                            <option value="">--Select Territory--</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <div class="col-md-2 align-left" style="padding-left:0px;">
                                        <b>Country Code</b>
                                        <div class="form-line">
                                            <input type="text" id="countryCode" name="countryCode" class="form-control" value="88" placeholder="88" disabled="" style="background: #E1E1E1; text-align: center;">
                                        </div>
                                    </div>  

                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <b>Mobile Number <span style="color: #ccc; font-size: 11px;">(Exampe : 01700000000)</span>  <span style="color: #FF0000;">*</span></b>
                                        <div class="form-line">
                                            <input type="text" id="mobile" name="mobile" class="form-control" value="" placeholder="Enter Mobile Number" required="" maxlength="11" minlength="11">
                                        </div>
                                    </div>                                    

                                    <div class="col-md-4 align-left" style="padding-left:0px;">
                                        <b>T & T</b>
                                        <div class="form-line">
                                            <input type="text" id="tandt" name="tandt" class="form-control" value="" placeholder="Enter T & T" maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <div class="col-md-8 align-left" style="padding-left:0px;">
                                        <b>Email </b>
                                        <div class="form-line">
                                            <input type="email" id="email" name="email" class="form-control" value="" placeholder="Enter Email" maxlength="45">
                                        </div>
                                    </div>                                    

                                    <div class="col-md-4 align-left" style="padding-left:0px;">
                                        <b>Date Of Birth</b>
                                        <div class="form-line">
                                            <input type="text" id="fromdate" name="dateofbirth" class="form-control" value="" placeholder="Enter Date Of Birth" readonly="">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <select id="type" name="type" class="form-control show-tick">
                                        <option value="0">END SHOP</option>
                                        <option value="1">DELAER</option> 
                                    </select>
                                </div>                                                             

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">                                        
                                        <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="serial" id="serial" value="{{ Request::segment(2) }}">
                            <input type="hidden" name="retailerid" id="retailerid" value="{{ Request::segment(3) }}">
                        </form>

                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">
                <div class="header">
                <h2>
                RETAILER LIST 
                </h2>
                </div>

                <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                <tr>
                <th>SL</th>
                <th>Retailer Name</th>
                <th>Status</th>                                
                </tr>
                </thead>
                <tfoot>
                <tr>
                <th>SL</th>
                <th>Retailer Name</th>
                <th>Status</th>                        
                </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultRetailer) > 0)   
                @php
                $serial =1;
                $status ='';
                @endphp

                @foreach($resultRetailer as $retailers)

                <tr>
                <th>{{ $serial }}</th>
                <th>{{ $retailers->name }}</th>                        
                <th>
                    @if($retailers->status==0)
                    <button type="button" class="btn bg-green waves-effect" title="Click To Inactive" data-toggle="modal" data-target="#retilerActiveOrInactive">Active</button>
                    @php
                     $status =1 
                    @endphp                  
                    @else
                    <button type="button" class="btn bg-red waves-effect" title="Click To Active" data-toggle="modal" data-target="#retilerActiveOrInactive">Inactive</button>
                    @php
                     $status =0 
                    @endphp 
                    @endif


                    <input type="hidden" name="status" id="status" value="{{$status}}">

                </th>                                        
                </tr>
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                <th colspan="3">No record found.</th>
                </tr>
                @endif    

                </tbody>
                </table>
                </div>
                </div>

                </div>
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection