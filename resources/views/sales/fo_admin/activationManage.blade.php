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

                        <form action="{{ URL('/activation-submit') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                            <div class="body">                                 

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="routes" name="routes" class="form-control show-tick" data-live-search="true" onchange="routeWiseRetailers(this.value)" required="">
                                        <option value="">-- Select Route --</option>
                                        @foreach($resultRoute as $routes)
                                            <option value="{{ $routes->route_id }}"> {{ $routes->rname }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group" id="retailerDivM">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="retailer" name="retailer" class="form-control show-tick" data-live-search="true" required="">
                                        <option value="">-- Select Retailer --</option>
                                        {{-- @foreach($resultRetailer as $retailer)
                                            <option value="{{ $retailer->retailer_id }}"> {{ $retailer->name }} </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="status" name="status" class="form-control show-tick" required="">
                                        <option value="">-- Select Status --</option>                                        
                                            <option value="0">Activation Request</option>
                                            <option value="1">Inactive Request</option>
                                    </select>
                                </div>
                               

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">                                        
                                        <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Request Send</button>                                        
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            Request List
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Route</th>
                                        <th>Retailer</th>                        
                                        <th>Status</th>                        
                                        <th>Done</th>       
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($activation) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($activation as $activations)                                       
                                    <tr>
                                        <th>{{ $serial }}</th> 
                                        <th>{{ $activations->rname }}</th>
                                        <th>{{ $activations->name }}</th>                                               
                                        <th>
                                            @if($activations->status==0)
                                                Activation Request
                                            @else
                                                Inactive Request
                                            @endif
                                        </th> 
                                        <th>
                                            @if($activations->done==2)
                                                Pending
                                            @else
                                                Done
                                            @endif
                                        </th> 
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <th colspan="5">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection