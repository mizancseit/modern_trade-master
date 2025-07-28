@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2> 
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Sales order manage
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
                                        <th>ORDER DATE</th>
										<th>FO</th>
                                        <th>CUSTOMER</th>
                                        <th>QTY</th>
                                        <th>VALUE</th>
                                        <th>STATUS</th>
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
                                    $totalQty  += $orders->total_order_qty;
                                    $totalValue += $orders->total_order_value;
                                    @endphp                    
                                    <tr>
                                       <th>
										<a href="{{ URL('/eshop-bucket-manage/'.$orders->order_id.'/'.$orders->customer_id.'/'.$orders->party_id) }}" title="Show Details" target="_blank">
											{{ $orders->order_no }}
										</a></br>
										
									</th>
									<th>{{ $orders->update_date }}</th>
									<th>{{ $orders->display_name }}</th>                        
									<th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
									<th>{{ $orders->total_order_qty }}</th>                        
									<th>{{ number_format($orders->total_order_value,2) }}</th>
                                    <th>{{ $orders->approval_status }}</th> 
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                        <th>{{ $totalQty }}</th>
                                        <th>{{ number_format($totalValue,2) }}</th>
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
            
        </div>
    </div>
</section>

@endsection