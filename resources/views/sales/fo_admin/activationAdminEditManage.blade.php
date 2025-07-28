@extends('sales.masterPage')
@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            ACTIVATION/INACTIVE REQUEST MANAGEMENT 
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

            <div class="alert alert-danger" id="errorMsg" style="display: none;">
              <strong>Warning!</strong> Location Field Required
            </div>

            <div class="alert alert-success" id="successMsg" style="display: none;">
              <strong>Successfully!</strong> {{ Auth::user()->display_name }} <span id="successMsgShow"></span>
            </div>

            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">                        

                        <form action="{{ URL('/admin/activation-submit') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->
                            <input type="hidden" name="id" id="id" value="{{ $request->id }}">

                            <div class="body">                                 

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="routes" name="routes" class="form-control show-tick" data-live-search="true" onchange="routeWiseRetailers(this.value)" required="">
                                        <!-- <option value="">-- Select Route --</option> -->
                                        @foreach($resultRoute as $routes)
                                            <option value="{{ $routes->route_id }}" @if($request->routeId==$routes->route_id) selected="" @endif> {{ $routes->rname }}  (Route) </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p></p>
                                
                                <div class="input-group" id="showHiddenDiv">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="retailer" name="retailer" class="form-control show-tick" data-live-search="true" required="">
                                        @foreach($resultRetailer as $retailer)
                                            <option value="{{ $retailer->retailer_id }}" @if($retailer->retailer_id==$request->retailerId) selected="" @endif> {{ $retailer->name }} (Retailer) </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="status" name="status" class="form-control show-tick" required="">
                                        @if($request->status==0)
                                            <option value="0">Activation Request</option>
                                        @else
                                            <option value="1">Inactive Request</option>
                                        @endif
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="done" name="done" class="form-control show-tick" required="">
                                        @if($request->done==2)
                                            <option value="">Please Select Approval Type</option>
                                            <option value="0">Active</option>
                                            <option value="1">Inactive</option>
                                        @elseif($request->done==0)
                                            <option value="0">Active</option>
                                            <option value="1">Inactive</option>
                                        @elseif($request->done==1)
                                            <option value="1">Inactive</option>
                                            <option value="0">Active</option>         
                                        @endif
                                    </select>
                                </div>
                               

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">                                        
                                        <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Request Save</button>                                        
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection