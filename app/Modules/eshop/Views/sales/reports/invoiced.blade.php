@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2> 
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Sales Order 
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
                    <h2>Sales Confirm & Stock Out </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y')}}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y')}}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <select id="executive_id" name="executive_id" class="form-control show-tick"> 
                                @foreach($managementlist as $row)
                                    <option value="{{ $row->id }}">{{$row->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="fos" name="fos" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Officer--</option> 
                                @foreach($officerlist as $row)
                                    <option value="{{ $row->id }}">{{ $row->email.' : '.$row->display_name }}</option>
                                @endforeach                                                   
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="makeInvoiceReport()">Search</button>
                        </div>
                    </div>
                    <div class="row">  
                        <div class="col-sm-2 text-center">                        
                           <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h2>
                            Order List
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
                                        <th>EXECUTIVE</th>
                                        <th>CUSTOMER</th>
                                        <th>ORDER QTY</th>
                                        <th>ORDER VALUE</th> 
                                        <th>DELIVERY QTY</th>
                                        <th>DELIVERY VALUE</th>
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
                                    $totalQty  += $orders->total_order_qty;
                                    $totalValue += $orders->total_order_value;
                                    $totalDeliveryQty  += $orders->total_delivery_qty;
                                    $totalDeliveryValue += $orders->total_delivery_value;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>
                                            <a href="{{ URL('/eshop-invoiced-view/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->order_no }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->first_name }}</th>
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                                           
                                        <th style="text-align: right;">{{ $orders->total_order_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_order_value,2) }}</th> 

                                          <th style="text-align: right;">{{ $orders->total_delivery_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_delivery_value,2) }}</th> 
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th> 
                                         <th style="text-align: right;">{{ $totalDeliveryQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDeliveryValue,2) }}</th>
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="9">No record found.</th>
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
<script type="text/javascript">
    function makeInvoiceReport()
          { 
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;
            var executive_id= document.getElementById('executive_id').value;


              document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/eshop-make-invoice-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                   document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                   
        }

</script>
@endsection
