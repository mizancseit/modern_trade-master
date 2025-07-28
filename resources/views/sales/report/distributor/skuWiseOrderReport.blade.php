@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        SKU WISE ORDER REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Category Wise Order 
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
                    <h2>SKU WISE ORDER REPORT</h2>                            
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
                            <select id="fos" class="form-control show-tick" data-live-search="true">
                                <option value="">------ Fo ------</option> 
                                @foreach($resultFO as $fos)
                                    <option value="{{ $fos->user_id }}">{{ $fos->user_id.' : '.$fos->first_name.''.$fos->middle_name.''.$fos->last_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="category" class="form-control show-tick" data-live-search="true" onchange="categoryWiseProduct()">
                                <option value="">------ Category ------</option> 
                                @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->catid }}">{{ $categories->catname }}</option>
                                @endforeach                                                    
                            </select>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-sm-3">
                            <div id="productsall">
                                <select id="products" class="form-control show-tick" data-live-search="true">
                                    <option value="">------ Product ------</option>   
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allSkuWiseOrder()">Search</button>
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
                                        <th>SL</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Order QTY</th>
                                        <th>Free QTY</th>
                                        <th>Wastage QTY</th>
                                        <th>Total QTY</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                
                                @if(sizeof($resultOrderList) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalFreeQty = 0;
                                    $totalWastageQty = 0;
                                    $totalFinal = 0;
                                    @endphp

                                    @foreach($resultOrderList as $orders)
                                    @php

                                    $totalQty  += $orders->orderQty;
                                    $totalFreeQty += $orders->freeQty;
                                    $totalWastageQty += $orders->totalWastageQty;
                                    $totalFinal += $orders->orderQty + $orders->totalWastageQty + $orders->freeQty;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>{{ $orders->cname }}</th>
                                        <th>{{ $orders->pname }}</th>
                                        <th>{{ $orders->orderQty }}</th>   
                                        <th>{{ $orders->freeQty }}</th>                     
                                        <th>{{ $orders->totalWastageQty }}</th>
                                        <th>{{ $orders->orderQty + $orders->freeQty + $orders->totalWastageQty }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="3" style="text-align: right;">Total : </th>                        
                                        <th>{{ $totalQty }}</th>
                                        <th>{{ $totalFreeQty }}</th>
                                        <th>{{ $totalWastageQty }}</th>
                                        <th>{{ $totalFinal }}</th>
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