@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        Retailer Remaining Commission Report
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Retailer Remaining Commission Report 
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
                    <h2>Retailer Remaining Commission Report</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <select id="routeID" class="form-control show-tick" data-live-search="true">
                                <option value="">------ Routes ------</option> 
                                @foreach($resultRoutes as $fos)
                                    <option value="{{ $fos->route_id }}">{{ $fos->rname }}</option>
                                @endforeach                                                    
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allRetailerCommission()">Search</button>
                        </div>
                    </div> 
                                                 
                </div>

                

            </div>
            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultRetailers) }} results 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Retailer</th>
                                        <th>Route</th>
                                        <th>Remaining Commission Balance</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultRetailers) > 0)   
                                    @php
                                    $serial =1;
                                    $totalBalance = 0;                                    
                                    @endphp

                                    @foreach($resultRetailers as $orders)
                                    @php

                                    $totalBalance  += $orders->totalBalance - $orders->totalBuyBalance;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>{{ $orders->name }}</th>
                                        <th>{{ $orders->rname }}</th>
                                        <th>{{ $orders->totalBalance - $orders->totalBuyBalance }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="3" style="text-align: right;">Total : </th>
                                        <th>{{ $totalBalance }}</th>
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="4">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection