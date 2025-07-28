@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        ORDER VS DELIVERY REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Order Vs Delivery 
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
                    <h2>ORDER VS DELIVERY REPORT</h2>                            
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

                        <div class="col-sm-5">
                            <select id="routes" class="form-control show-tick" data-live-search="true">
                                <option value=""> All </option> 
                                @foreach($resultRoute as $routes)
                                    <option value="{{ $routes->route_id }}">{{ $routes->rname }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allFoOrderVsDelivery()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>
            
            <div id="showHiddenDiv">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultOrderList) }} results 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>ORDER</th>
                                        <th>DATE</th>                        
                                        <th>CUSTOMER</th>
                                        <th style="text-align: right;">QTY</th>
                                        <th style="text-align: right;">VALUE</th>
                                        <th style="text-align: right;">DELIVERY QTY</th>
                                        <th style="text-align: right;">VALUE</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultOrderList) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalValue = 0;
                                    $totalDeliveryQty = 0;
                                    $totalDeliveryValue = 0;

                                    @endphp

                                    @foreach($resultOrderList as $orders)
                                    @php
                                    $totalQty  += $orders->total_qty;
                                    $totalValue += $orders->total_value;
                                    $totalDeliveryQty  += $orders->total_delivery_qty;
                                    $totalDeliveryValue += $orders->total_delivery_value;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $orders->order_no }}</th>
                                        <th>{{ $orders->order_date }}</th>                                           
                                        <th>{{ $orders->name }}</th>                        
                                        <th style="text-align: right;">{{ $orders->total_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_value,2) }}</th>
                                        <th style="text-align: right;">@if($orders->total_delivery_qty!=null){{ $orders->total_delivery_qty }} @else 0 @endif</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_delivery_value,2) }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="3" style="text-align: right;">Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                        <th style="text-align: right;">{{ $totalDeliveryQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDeliveryValue,2) }}</th>
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="7">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- For Print --}}
                <div class="card">
                    <div class="row" style="text-align: center; padding: 10px 10px; ">
                        <div class="col-sm-12">
                            <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                <i class="material-icons">print</i>
                                <span>PRINT...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection