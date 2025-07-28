@extends('sales.masterPage')
@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            ATTENDANCE MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div>                    
                </div>                
            </div>

            

            <div class="alert alert-danger" id="errorMsg" style="display: none;">
              <strong>Warning!</strong>  Route or Location field empty!
            </div>

            @if(Session::has('successAttendance'))
                <div class="alert alert-warning">
                {{ Session::get('successAttendance') }}                        
                </div>
            @endif

            

            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        <div class="header">
                            <h2>ATTENDANCE</h2>                            
                        </div>

                       {{--  <div id="embedMap" style="width: 100%; height: 100px;">
                            
                        </div> --}}

                        <form action="{{ URL('/attendance-in-out') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                            <div class="body"> 
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="distributor" name="distributor" class="form-control show-tick" data-live-search="true" required="">
                                            {{-- <option value="">-- Select Distributor --</option> --}}
                                        @foreach($resultDistributor as $distributor)
                                            <option value="{{ $distributor->id }}"> {{ $distributor->display_name }} </option>
                                        @endforeach 
                                    </select>
                                </div>
                                <p></p>

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="routes" name="routes" class="form-control show-tick" data-live-search="true" onchange="routeWiseRetailers(this.value)"  required="">
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

                                    <select id="retailer" name="retailer" class="form-control show-tick" data-live-search="true">
                                        <option value="">-- Select Retailer --</option>
                                        {{-- @foreach($resultRetailer as $retailer)
                                            <option value="{{ $retailer->retailer_id }}"> {{ $retailer->name }} </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="col-md-12 align-left" style="padding-left:0px;">
                                        <div class="form-line">
                                            <input type="text" id="location" name="location" class="form-control" value="" placeholder="Location" required="" maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                @php
                                $inOutStatus = 1;
                                //echo sizeof($resultInOut);
                                if(sizeof($resultInOut)>0)
                                {
                                    $inOutStatus = 3;
                                }

                                @endphp

                                <input type="hidden" name="inOutStatus" id="inOutStatus" value="{{ $inOutStatus }}">

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">
                                        @if($inOutStatus==1)
                                        <button type="button" id="in" class="btn bg-pink btn-block btn-lg waves-effect" onclick="getLocation()">IN</button>
                                        <button type="button" id="out" class="btn bg-pink btn-block btn-lg waves-effect" onclick="getLocation()" style="display: none;">OUT</button>
                                        @else
                                        <button type="button" id="out" class="btn bg-pink btn-block btn-lg waves-effect" onclick="getLocation()">OUT</button>
                                        <button type="button" id="in" class="btn bg-pink btn-block btn-lg waves-effect" onclick="getLocation()" style="display: none;">IN</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            <div class="alert alert-success" id="successMsg" style="display: none;">
              <strong>Successfully!</strong> {{ Auth::user()->display_name }} <span id="successMsgShow"></span>
            </div>

            <div id="showHiddenDiv12">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            TODAY'S ATTENDANCE{{-- About {{ sizeof($resultAttendanceList) }} results --}} 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>FO</th>
                                        <th>DATE</th>                        
                                        <th>LOCATION</th>                        
                                        <th>IN TIME</th>                        
                                        <th>OUT TIME</th>                        
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultAttendanceList) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($resultAttendanceList as $visits)                                       
                                    <tr>
                                        <th>{{ $serial }}</th> 
                                        <th>{{ $visits->first_name }}</th>
                                        <th>{{ date('d M Y', strtotime($visits->date)) }}</th>                                               
                                        <th>{{ $visits->location }}</th>                                               
                                        <th>{{ date('H:i', strtotime($visits->entrydatetime)) }}</th>
                                        <th> 
                                        @php
                                        $out = DB::table('ims_attendence')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('foid', Auth::user()->id)
                                        ->where('type', 3)
                                        ->where('date', $visits->date)
                                        ->groupBy('date')
                                        ->max('entrydatetime');

                                        $outTime = '';
                                        if(sizeof($out)>0)
                                        {
                                            $outTime = date('H:i', strtotime($out));
                                        }
                                        @endphp
                                        
                                        {{ $outTime }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <th colspan="6">No record found.</th>
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


<!-- for GEO Location -->
<!-- <script src="https://maps.google.com/maps/api/js?sensor=true"></script> -->
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

        function showPosition1(){
           
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(success, error);
            } else{
                alert("Sorry, your browser does not support HTML5 geolocation.");
            }
        }     
    
        //navigator.geolocation.getCurrentPosition(success, error);

        function success(position) {

            var GEOCODING = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + position.coords.latitude + '%2C' + position.coords.longitude + '&language=en';

            $.getJSON(GEOCODING).done(function(location) {
                //alert(location.results[0].formatted_address);
                //alert(location.results[0].formatted_address);
                //$('#location1').html(location.results[0].formatted_address);
                document.getElementById("location").value=location.results[0].formatted_address;
                // $('#country').html(location.results[0].address_components[5].long_name);
                // $('#state').html(location.results[0].address_components[4].long_name);
                // $('#city').html(location.results[0].address_components[2].long_name);
                // $('#location').html(location.results[0].formatted_address);
                // $('#latitude').html(position.coords.latitude);
                // $('#longitude').html(position.coords.longitude);
            });

            lat = position.coords.latitude;
            long = position.coords.longitude;
            var latlong = new google.maps.LatLng(lat, long);
          
            var myOptions = {
                center: latlong,
                zoom: 16,
                mapTypeControl: true,
                navigationControlOptions: {style:google.maps.NavigationControlStyle.SMALL}
            }
            
            var map = new google.maps.Map(document.getElementById("embedMap"), myOptions);
            var marker = new google.maps.Marker({position:latlong, map:map, title:"You are here!"});

        }

        // Define callback function for failed attempt
        function showError(error){
            if(error.code == 1){
                result.innerHTML = "You've decided not to share your position, but it's OK. We won't ask you again.";
            } else if(error.code == 2){
                result.innerHTML = "The network is down or the positioning service can't be reached.";
            } else if(error.code == 3){
                result.innerHTML = "The attempt timed out before it could get the location data.";
            } else{
                result.innerHTML = "Geolocation failed due to unknown error.";
            }
        }

        function error(err) {
            console.log(err)
        }
</script>
@endsection