@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2> 
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Status
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
            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h2>
                            Requisition status
                        </h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>ORDER NO</th>
                                        <th>COLLECT DATE</th>
                                        <th>OFFICER</th>
                                        <th>CUSTOMER</th>
                                        <th>QTY</th>
                                        <th>VALUE</th> 
                                        <th>STATUS</th> 
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($orders) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalValue = 0;

                                    @endphp

                                    @foreach($orders as $order)
                                    @php 
                                    $totalQty  += $order->total_order_qty;
                                    $totalValue += $order->total_order_value;
                                    @endphp                    
                                    <tr>
                                        <td>{{ $serial }}</td>
                                        <td>{{ $order->order_no }}
                                            <!-- <a href="{{ URL('/eshop-requisition-view/'.$order->order_id.'/'.$order->fo_id) }}" title="Confirm Delivery">
                                                {{ $order->order_no }}
                                            </a> -->
                                        </td>
                                        <td>{{ $order->order_date }}</td>
                                        <td> @if($order->userfo) {{ $order->userfo->display_name }} @endif</td>                        
                                        <td> @if($order->party) {{ $order->party->name }} <br /> {{ $order->mobile }} @endif</td>
                                        <td style="text-align: right;">{{ $order->total_order_qty }}</td>
                                        <td style="text-align: right;">{{ number_format($order->total_order_value,2) }}</td> 
                                        <td style="text-align: right;">{{ $order->approval_status }}</td>  
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                       
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="8">No record found.</th>
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