@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PRODUCT WISE REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Product Wise Report 
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
                    <h2>PRODUCT WISE REPORT</h2>                            
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
                            <select id="category" class="form-control show-tick" data-live-search="true" onchange="categoryWiseProductFo()">
                                <option value="">------ Category ------</option> 
                                @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->catid }}">{{ $categories->catname }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <div id="productsall">
                                <select id="products" class="form-control show-tick" data-live-search="true">
                                    <option value="">------ Product ------</option>   
                                </select>
                            </div>
                        </div>

                    </div> 
                    <div class="row">
                        
                        <div class="col-sm-5"></div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allProductWise()">Search</button>
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
                                        <th>SL</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Order QTY</th>                        
                                        <th>Delivery QTY</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultOrderList) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalDeliveryQty = 0;                    
                                    @endphp

                                    @foreach($resultOrderList as $orders)
                                    @php
                                    $totalQty  += $orders->order_qty;
                                    $totalDeliveryQty += $orders->delivered_qty;                    
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>{{ $orders->catname }}</th>
                                        <th>{{ $orders->pname }}</th>
                                        <th>{{ $orders->order_qty }}</th>                        
                                        <th>@if($orders->delivered_qty!=null) {{ $orders->delivered_qty }} @else - @endif</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="3" style="text-align: right;">Total : </th>                        
                                        <th>{{ $totalQty }}</th>
                                        <th>{{ $totalDeliveryQty }}</th>                        
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="5">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- For Print --}}
                @if(sizeof($resultOrderList) > 0)
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
                @endif
            </div>
        </div>
    </div>
</section>

@endsection