@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Analysis List 
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
                    <h2>Analysis Sales Order </h2>                            
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
                            <select id="customer_id" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Customer --</option> 
                                @foreach($customerResult as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->sap_code.' : '.$customer->name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="category" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Category --</option> 
                                @foreach($categoryResult as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allEshopOrderAnalysis()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h2>
                            Analysis List
                        </h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>CAT NAME</th>
                                        <th>SAP CODE</th>
                                        <th>PRODUCT NAME</th>
                                        <th>QTY</th>
                                        <th>UPDATE QTY</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <form action="{{ URL('/eshop-orderDelivery-open-submit') }}" method="POST">
                                    {{ csrf_field() }}
                                    @if(sizeof($resultCartPro) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalValue = 0;
                                    $sap_code = array();
                                    $pro_ids = array();
                                    @endphp

                                    @foreach($resultCartPro as $orders)
                                    @php 
                                    $currentStock = $orders->stock_qty;

                                    $totalQty  += $orders->total_qty;
                                    if (in_array($orders->sap_code, $sap_code)){                           
                                        $subvalu = $currentStock-$orders->total_qty;
                                        $currentStock = $subvalu;
                                    } else { 
                                        array_push($sap_code,$orders->sap_code);
                                    }
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>                                        
                                        <th>{{ $orders->catname }}</th>
                                        <th>
                                            <a href="{{ URL('/eshop-product-wise-analysis/'.$orders->pid) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->sap_code }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->pname }}</th>                                           
                                        <th style="text-align: right;">{{ $orders->total_qty }} </th>  
                                        <th style="text-align: right;">{{ $orders->total_approved_qty }}</th>  

                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                    </tr>
                                    <!-- <tr>
                                        <th colspan="7" style="text-align: right;">
                                        <button class="btn btn-lg btn-primary">Submit</button></th>
                                    </tr> -->
                                    @else
                                    <tr>
                                        <th colspan="7">No record found.</th>
                                    </tr> 
                                    @endif   
                                    </form>                                     
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