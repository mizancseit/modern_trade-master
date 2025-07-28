@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Return Delivery 
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
                    <h2>Return Report </h2>                            
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
                        <div class="col-sm-4">
                            <select id="customer" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Customer --</option> 
                                @foreach($customer as $cus)
                                    <option value="{{ $cus->customer_id }}">{{ $cus->name.' : '.$cus->sap_code }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="returnDeliveryReport()">Search</button>
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
                                        <th>RETURN</th>
                                        <th>DELIVERY DATE</th>
                                        <th>EXECUTIVE</th>
                                        <th>CUSTOMER</th>
                                        <th>QTY</th>
                                        <th>VALUE</th>
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
                                    $totalQty  += $orders->total_delivery_qty;
                                    $totalValue += $orders->total_delivery_value;
                                    @endphp                    
                                    <tr>
                                        <th>
                                            <a href="{{ URL('/eshop-return-delivery-report-details/'.$orders->return_id) }}" title="Show Details" target="_blank">
                                                {{ $orders->return_no }}
                                            </a>
									    </th>
                                        <th>{{ $orders->update_date }}</th>
                                        <th>{{ $orders->first_name }}</th>                        
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                                        <th>{{ $orders->total_delivery_qty }}</th>                        
                                        <th>{{ number_format($orders->total_delivery_value,2) }}</th>
										
										
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