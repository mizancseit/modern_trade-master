@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        ORDER MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Order Manage 
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
                    <h2>ORDER MANAGE</h2>                            
                </div>
                
                <div class="body">

                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                <th style="text-align: center;">Print</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($resultOrderList as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th>{{ $serial }}</th>
                                <th>
                                    <a href="{{ URL('/all-invoice-exception/'.$orders->order_id.'/'.$orders->retailer_id.'/'.$orders->route_id.'/'.$orders->offer_type) }}" title="Click To Edit Invoice" target="_blank">
                                        {{ $orders->order_no }}
                                    </a>
                                </th>
                                <th>{{ $orders->ordered_date }}</th>
                                <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th>{{ $orders->total_qty }}</th>                        
                                <th>{{ number_format($orders->total_value,2) }}</th>
                                <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details-exception/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th>
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                <th></th>
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
    </div>
</section>

@endsection