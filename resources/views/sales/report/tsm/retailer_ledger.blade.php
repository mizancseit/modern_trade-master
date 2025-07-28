
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        Retailer Ledger Report
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
                        </small>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success">
        {{ Session::get('success') }}                        
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Retailer Ledger Report</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-3">
                            <select id="channel" class="form-control show-tick" data-live-search="true">
                               <option value="{{ $channelName->business_type_id }}" selected="">{{ $channelName->business_type }}</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="divistion" class="form-control show-tick" onchange="tsmDivisionWisePoints1()">
                                <option value=""> Select Division </option>
                                @foreach($divisionName as $divisions)
                                    <option value="{{ $divisions->iDivId }}">{{ $divisions->div_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="row"> 
                        <div class="col-sm-3" id="foPoints">
                            <select  class="form-control show-tick" name="pointID" id="pointID">
                                <option value=""> Point </option>
                            </select>
                        </div>

                        <div class="col-sm-3" id="div_route">
                            <select class="form-control show-tick" name="routeID" id="routeID">
                                <option value="">Select Route</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="tmsDBWiseRetailerLedgerReport()">Search</button>
                        </div>
                        <div class="col-sm-2">
                             <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                
            </div>
        </div>
    </div>
</section>

@endsection