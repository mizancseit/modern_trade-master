@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2> 
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Order 
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
                    <h2>DELIVERY REPORT </h2>                            
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
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="deliveryList()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
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
                                        <th>SL.</th>
                                        <th>Sales Order No</th> 
                                        <th>Order Date</th>
                                        <th>Delivery Date</th>
                                        <th>Officer Name</th>
                                        <th>Executive Name</th>
                                        <th>Customer Name</th>
                                        <th>Outlet Name</th>
                                        <th>Order QTY</th>
                                        <th>Order VALUE</th>
                                        <th>Net Value</th>
                                        <th>Delivary qty</th>
                                        <th>Delivary value</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @if(sizeof($resultOrderList) > 0)   
                                    @php
                                    $serial =1;
                                    $totalOrderQty = 0;
                                    $totalOrderValue = 0;
                                    $totalDelivaryQty = 0;
                                    $totalDeliveryValue = 0;
                                    $totalOrderCommission = 0;
                                    @endphp
                                    @foreach($resultOrderList as $orders)
                                    @php
                                    $totalOrderQty  += $orders->total_order_qty;
                                    $totalOrderValue += $orders->total_order_value;
                                    $totalDelivaryQty  += $orders->total_delivery_qty;
                                    $totalDeliveryValue += $orders->total_delivery_value; 
                                    $orderCommission = DB::table('mts_categroy_wise_commission') 
                                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
                                        ->where('order_id', $orders->order_id) 
                                        ->first();
                                    $totalOrderCommission += $orderCommission->commission;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>
                                            <a href="{{ URL('/modernorder-details/'.$orders->order_id) }}" title="Show Details" target="_blank">
                                                {{ $orders->order_no }}
                                            </a></br>
                                            
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->delivery_date }}</th>
                                        <th>{{ $orders->display_name }}</th>
                                        <th>{{ DB::table('users')->where('id',$orders->executive_id)->first()->display_name}}</th>
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                                        <th>{{ DB::table('mts_party_list')->where('party_id',$orders->party_id)->first()->name }}</th>                        
                                        <th>{{ $orders->total_order_qty }}</th>                        
                                        <th>{{ number_format($orders->total_order_value,2) }}</th> 
                                        <th>   
                                            {{ number_format($orders->total_order_value-$orderCommission->commission,2) }}
                                        </th> 
                                        <th>{{ $orders->total_delivery_qty }}</th>  
                                        <th>{{ number_format($orders->total_delivery_value, 2) }}</th> 
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="8" style="text-align: right;">Grand Total : </th>                        
                                        <th>{{ $totalOrderQty }}</th>
                                        <th>{{ number_format($totalOrderValue,2) }}</th>
                                        <th>{{ number_format(($totalOrderValue - $totalOrderCommission),2) }}</th>
                                        <th>{{ $totalDelivaryQty }}</th>
                                        <th>{{ number_format($totalDeliveryValue,2) }}</th>
                                    </tr>

                                    @else
                                    <tr>
                                        <th colspan="13">No record found.</th>
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
