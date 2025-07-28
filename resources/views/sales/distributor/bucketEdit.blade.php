@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            DELIVERY ENTRY
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Order
                            </small>
                        </h2>
                    </div>


                    <?php 
                    
                    if($resultInvoice->order_status != 'Closed') {
                    
                    ?>
                    
                    <div class="col-sm-3">
                         <a href="{{ URL('/close-distributor-order/'.$resultInvoice->order_id.'/'.Request::segment(4)) }}"> 
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Close Order Request</button>
                        </a> 
                    </div>
                    
                    <?php } ?>
                    
                </div>
            </div>
        </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif

                
            
            <form action="{{ URL('/order-edit-submit') }}" name="form" id="form" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">                        

                            @if(sizeof($resultCartPro)>0)
                            
                            <div class="header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <strong>To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                        Owner &nbsp;&nbsp; : {{ $resultInvoice->owner }}<br />
                                        Mobile &nbsp; : {{ $resultInvoice->mobile }}<br />
                                        Address &nbsp;&nbsp; : {{ $resultInvoice->vAddress }}
                                    </strong>
                                    </div>

                                    <div class="col-sm-4">
                                        <strong>
                                        Collected By &nbsp;&nbsp; : {{ $resultInvoice->display_name }}<br />
                                        Invoice No : {{ $resultInvoice->order_no }}<br />
                                        Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->order_date)) }}</strong>
                                    </div>
                                </div>                                                           
                            </div>

                            <div class="header">
                                <h2>DELIVERY ENTRY</h2>                            
                            </div>

                            <div class="body">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product Name</th>
                                                <th>Order</th>
                                                <th>Value</th>
                                                <th>Delivery</th>
                                                <th>D.Value</th>
                                                <th>Free</th>
                                                <th>F.Value</th>
                                                <th>Wastage</th>
                                                <th>W.Value</th>                          
                                                <th>Replace</th>                                            
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            @php
                                                $commissionCategoriesEx  = DB::table('tbl_except_category_commission')
                                                        ->select('categoryId')
                                                        ->where('status',0)
                                                        ->where('global_company_id',Auth::user()->global_company_id)
                                                        ->get();

                                                $data = collect($commissionCategoriesEx)->map(function($x){ return (array) $x; })->toArray();
                                            @endphp

                                            @if(sizeof($resultCartPro)>0)
                                            @php
                                            $serial   = 1;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalQty = 0;
                                            $totalWastage = 0;
                                            $totalWastageValue = 0;
                                            $totalPrice = 0;
                                            $totalDeliveryPrice = 0;
                                            $totalFreeQty =0;
                                            $totalFreeValue =0;
                                            $subTotalCommissionOnly =0;
                                            @endphp
                                            @foreach($resultCartPro as $items)                                       
                                            <tr>
                                                <th colspan="11">{{ $items->catname }}</th>
                                            </tr>

                                                @php
                                                $resultProComm  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.partial_order_id','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.delivered_qty','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                    //->where('tbl_order.order_type','Confirmed')                        
                                                    ->where('tbl_order.fo_id',$foMainId)                        
                                                    ->where('tbl_order_details.order_id',$orderMainId)
                                                    //->where('tbl_order_details.partial_order_id',$items->partial_order_id)
                                                    ->where('tbl_order_details.cat_id', $items->catid)
                                                    ->whereNotIn('tbl_order_details.cat_id', $data)    
                                                    ->get();
                                                 
                                                @endphp
                                                @foreach ($resultProComm as $itemsPro)
                                                    @php
                                                        $subTotalCommissionOnly += $itemsPro->p_total_price;
                                                    @endphp
                                                @endforeach

                                                @php 
                                                $itemsCount = 1;
                                                $reultPro  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.partial_order_id','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.delivered_qty','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.wastage_value','tbl_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                       
                                                    ->where('tbl_order.fo_id',$foMainId)                        
                                                    ->where('tbl_order_details.order_id',$orderMainId)
                                                    ->where('tbl_order_details.partial_order_id',$items->partial_order_id)
                                                    ->where('tbl_order_details.cat_id', $items->catid)    
                                                    ->get();
                                                   //dd($reultPro);
                                                @endphp
                                                @foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalQty += $itemsPro->order_qty;
                                                $totalWastage += $itemsPro->wastage_qty;
                                                $totalWastageValue += $itemsPro->wastage_qty * $itemsPro->depo;
                                                $totalPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;

                                                if($itemsPro->delivered_qty>= -1)
                                                {                                                 
                                                  $totalDeliveryPrice += $itemsPro->delivered_qty * $itemsPro->p_unit_price;
                                                }
                                                else
                                                {                                                  
                                                  $totalDeliveryPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;
                                                }                                                 

                                                @endphp

                                                <tr>
                                                <th>{{ $serial }}</th>
                                                <th>{{ $itemsPro->proname }}</th>
                                                <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                                <th style="text-align: right;">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</th>
                                                
                                               <th style="text-align: right;">
                                                    <input type="number" name="qty[]" id="qty{{ $serial }}" value="@if($itemsPro->delivered_qty>= -1){{ $itemsPro->delivered_qty }}@else{{ $itemsPro->order_qty }}@endif" class="sumMasudQty form-control" style="width: 100px;" onkeyup="qty({{ $serial }},{{ $itemsPro->order_qty }})" min="1" autocomplete="off">
                                                </th>
                                                
												<?php 
												
												if($itemsPro->delivered_qty>= -1)
												  $act_qnty = $itemsPro->delivered_qty;
											    else
												  $act_qnty = $itemsPro->order_qty;
												
												
												?>
												
												<th style="text-align: right;" id="rowPrice{{ $serial }}">{{ number_format($act_qnty * $itemsPro->p_unit_price,0) }}</th>
                                                
												<input type="hidden" name="oldqty[]" id="oldqty{{ $serial }}" value="{{ $itemsPro->order_qty }}">
                                                <input type="hidden" name="oldprice[]" id="oldprice{{ $serial }}" value="{{ $act_qnty * $itemsPro->p_unit_price }}">
                                                <input type="hidden" name="price[]" id="price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}" >
                                                <input type="hidden" name="product_id[]" id="product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >
                                                <th style="text-align: right;">-</th>
                                                <th style="text-align: right;">-</th>
                                                <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty }} @endif</th>
                                               <th style="text-align: right;">{{ number_format($itemsPro->wastage_qty * $itemsPro->depo,2) }} </th>
                                                <th style="text-align: right;">
                                                    <input type="text" name="replaceDelivery[]" id="replaceDelivery{{ $serial }}" value="@if($itemsPro->wastage_qty==''){{0}}@else{{$itemsPro->wastage_qty}} @endif" class="form-control" style="width: 50px;">
                                                </th>
                                                </tr>
                                                @php
                                                $serial ++;
                                                @endphp
                                                @endforeach


                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                    ->select('fq.type','fq.status','fq.order_id','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','fq.free_value','fq.total_free_value','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo','fq.free_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.type','R')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->where('fq.status', 0)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->total_free_qty;
                                                $totalFreeValue += $itemsPro->total_free_value;
                                                
                                                // REGULAR OFFER AND OPTION QUERY HERE
                                                $reultRegularAnd  = DB::table('tbl_order_regular_and_free_qty AS fq')
                                                ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_value','fq.total_free_value','fq.special_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                // ->where('fq.status','0')      
                                                ->where('fq.order_id',$resultInvoice->order_id)
                                                ->where('fq.catid', $items->catid)
                                                ->where('fq.special_id', $itemsPro->free_id)
                                                ->first();
                                                @endphp
                                                    <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th>{{ $itemsPro->proname }} [ FREE ]</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"></th>
                                                    
                                                    <th style="text-align: right;">
                                                    <input type="hidden" name="free_id[]" id="free_id{{ $serial }}" value="{{ $itemsPro->free_id }}" >
                                                    <input type="hidden" name="total_free_qty[]" id="total_free_qty{{ $serial }}" value="{{ $itemsPro->total_free_qty }}" >
                                                    <input type="hidden" name="free_product_id[]" id="free_product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >
                                                    <input type="hidden" name="free_value[]" id="free_value{{ $serial }}" value="{{ $itemsPro->depo }}" >
                                                    <input type="number" name="freeqty[]" id="freeqty{{ $serial }}" value="@if($itemsPro->total_free_qty=='') {{ $itemsPro->total_free_qty}}@else{{$itemsPro->free_delivery_qty}}@endif" class="sumMasudQtyFree form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off">
                                                    </th>
                                                    <th style="text-align: right;">
                                                        <input type="hidden" name="total_free_value[]" id="total_free_value{{ $serial }}" value="{{ $itemsPro->free_value }}" >
                                                        {{ $itemsPro->free_value }}</th>
                                                    
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"> </th>    
                                                    
                                                    @endif
                                                    </tr>


                                                    @if(sizeof($reultRegularAnd)>0)
                                                        @php
                                                        $totalFreeQty += $reultRegularAnd->total_free_qty;
                                                         $totalFreeValue += $reultRegularAnd->total_free_value;
                                                        $serial ++;                                                    
                                                        @endphp 
                                                            <tr>
                                                            <th>{{ $serial }}</th>
                                                            <th>{{ $reultRegularAnd->proname }} [ FREE ]</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;"></th>
                                                            <th style="text-align: right;">
                                                                <input type="hidden" name="and_free_id[]" id="and_free_id{{ $serial }}" value="{{ $reultRegularAnd->free_id }}" >
                                                                <input type="hidden" name="and_free_qty[]" id="and_free_qty{{ $serial }}" value="{{ $reultRegularAnd->total_free_qty }}" >

                                                                 <input type="hidden" name="and_free_pid[]" id="and_free_pid{{ $serial }}" value="{{ $reultRegularAnd->product_id }}" >
                                                                 <input type="hidden" name="and_free_value[]" id="and_free_value{{ $serial }}" value="{{ $reultRegularAnd->depo }}" >
                                                                <input type="number" name="andFreeQty[]" id="andFreeQty{{ $serial }}" value="@if($reultRegularAnd->total_free_qty==''){{ $reultRegularAnd->total_free_qty}}@else {{ $reultRegularAnd->free_delivery_qty}}@endif" class="sumMasudQtyFree form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off">
                                                            </th>
                                                            <th style="text-align: right;">
                                                        <input type="hidden" name="total_and_free_value[]" id="total_and_free_value{{ $serial }}" value="{{ $reultRegularAnd->free_value }}" >
                                                        {{ $reultRegularAnd->free_value }}</th>
                                                            @if(Auth::user()->user_type_id==5)
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;"> </th>
                                                            <th style="text-align: right;"> </th>
                                                            @endif
                                                            </tr>
                                                        @endif

                                                @php
                                                $serial ++;
                                                @endphp                       
                                                @endforeach

                                                {{-- SPECIAL OFFER --}}    
                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_special_free_qty AS fq')
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_value','fq.total_free_value','fq.free_delivery_qty','fq.free_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.status','0')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->total_free_qty;
                                                 $totalFreeValue += $itemsPro->total_free_value;
                                                
                                                // SPECIAL OFFER AND OPTION QUERY HERE
                                                $reultSpecialAnd  = DB::table('tbl_order_special_and_free_qty AS fq')
                                                ->select('fq.free_id','fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_value','fq.total_free_value','fq.free_delivery_qty','fq.special_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                // ->where('fq.status','0')      
                                                ->where('fq.order_id',$resultInvoice->order_id)
                                                ->where('fq.catid', $items->catid)
                                                ->where('fq.special_id', $itemsPro->free_id)
                                                ->first();
                                                @endphp
                                                    <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th>{{ $itemsPro->proname }} [ SP FREE ]</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                     @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>     
                                                    
                                                    @endif

                                                    <th style="text-align: right;">
                                                        <input type="hidden" name="free_id[]" id="free_id{{ $serial }}" value="{{ $itemsPro->free_id }}" >
                                                        <input type="hidden" name="total_free_qty[]" id="total_free_qty{{ $serial }}" value="{{ $itemsPro->total_free_qty }}" >
                                                        <input type="hidden" name="free_product_id[]" id="free_product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >
                                                        <input type="hidden" name="free_value[]" id="free_value{{ $serial }}" value="{{ $itemsPro->depo }}" >
                                                        <input type="number" name="freeqty[]" id="freeqty{{ $serial }}" value="@if($itemsPro->free_delivery_qty==''){{$itemsPro->total_free_qty}}@else{{$itemsPro->free_delivery_qty}}@endif" class="sumMasudQtyFree form-control" style="width: 70px;" onkeyup="freeQtyNew({{ $serial }})" min="1" autocomplete="off">


                                                        <input type="hidden" id="oldFreeQty{{ $serial }}" value="@if($itemsPro->free_delivery_qty==''){{$itemsPro->total_free_qty}}@else{{$itemsPro->free_delivery_qty}}@endif">

                                                        <input type="hidden" id="oldFreeValue{{ $serial }}" value="$itemsPro->depo">
                                                    </th>
                                                    <th style="text-align: right;" id="freeValueChange{{ $serial }}">
                                                        <input type="hidden" name="total_free_value[]" id="total_free_value{{ $serial }}" value="{{ $itemsPro->total_free_qty * $itemsPro->depo }}" >
                                                        {{ number_format($itemsPro->total_free_qty * $itemsPro->depo ,2) }}

                                                    </th>
                                                    
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">-</th>     
                                                    <th style="text-align: right;">-</th>       
                                                    <th style="text-align: right;">-</th>
                                                    @endif
                                                    </tr>

                                                    @if(sizeof($reultSpecialAnd)>0)
                                                    @php
                                                    $totalFreeQty += $reultSpecialAnd->total_free_qty;
                                                     $totalFreeValue += $reultSpecialAnd->total_free_value;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <th>{{ $serial }}</th>
                                                        <th>{{ $reultSpecialAnd->proname }} [ SP FREE ]</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"></th>
                                                        <th style="text-align: right;">
                                                            <input type="hidden" name="and_free_id[]" id="and_free_id{{ $serial }}" value="{{ $reultSpecialAnd->free_id }}" >
                                                            <input type="hidden" name="and_free_qty[]" id="and_free_qty{{ $serial }}" value="{{ $reultSpecialAnd->total_free_qty }}" >
                                                             <input type="hidden" name="and_free_pid[]" id="and_free_pid{{ $serial }}" value="{{ $reultSpecialAnd->product_id }}" >
                                                             <input type="hidden" name="and_free_value[]" id="and_free_value{{ $serial }}" value="{{ $reultSpecialAnd->depo }}" >
                                                    <input type="number" name="andFreeQty[]" id="andFreeQty{{ $serial }}" value="@if($reultSpecialAnd->free_delivery_qty==''){{$reultSpecialAnd->total_free_qty}}@else{{$reultSpecialAnd->free_delivery_qty}}@endif" class="sumMasudQtyFree form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off">
                                                    </th>
                                                   <th style="text-align: right;">
                                                        <input type="hidden" name="total_and_free_value[]" id="total_and_free_value{{ $serial }}" value="{{ $reultSpecialAnd->total_free_qty * $reultSpecialAnd->depo }}" >
                                                        {{ number_format($reultSpecialAnd->total_free_qty * $reultSpecialAnd->depo ,2) }}</th>
                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        @endif
                                                        </tr>
                                                    @endif

                                                @php
                                                $serial ++;
                                                @endphp                       
                                                @endforeach                                             
                                            </tr>
                                            
                                            @endforeach
                                            @endif

                                            <tr>
                                                <th colspan="2" align="right">Grand Total</th>
                                                <th style="text-align: right;">{{ number_format($totalQty,0) }}</th>
                                                <th style="text-align: right;">{{ number_format($totalPrice,0) }}</th>
                                                
                                                <th style="text-align: right;" id="totalQty">{{ number_format($totalQty,0) }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <input type="hidden" name="totalHiddenPriceSum" id="totalHiddenPriceSum" value="{{ $totalPrice }}">
                                                <th style="text-align: right;" id="totalPrice">{{ number_format($totalDeliveryPrice,0) }}
                                                    <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                                                </th>
                                                <th style="text-align: right;" id="totalFreeQty">{{ number_format($totalFreeQty,0) }}</th>
                                                <th style="text-align: right;" id="totalFreeValueM">
                                                    {{ number_format($totalFreeValue,2) }}
                                                    <input type="hidden" id="totalHiddenFreeValue" value="{{ $totalFreeValue }}">
                                                </th>
                                                <th style="text-align: right;">{{ number_format($totalWastage,0) }}</th>
                                                <th style="text-align: right;">{{ number_format($totalWastageValue,2) }}</th>
                                                <th>&nbsp;</th>                                            
                                            </tr>
                                            
                                            @php
                                            $com = 0;
                                            $comValue = 0;
                                            $reultProRate  = DB::select("SELECT * FROM tbl_commission WHERE businessType='".Auth::user()->business_type_id."' AND '$subTotalCommissionOnly' BETWEEN minSlab AND maxSlab LIMIT 1");

                                            if(sizeof($reultProRate)>0)
                                            {
                                                $com += $reultProRate[0]->rat;
                                            @endphp

                                            <!-- <tr>
                                                <th colspan="2" align="left" id="memoCommission">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;" id="memoCommissionPrice">
                                                @if(sizeof($reultProRate)>0)                                                
                                                    {{  $comValue = number_format(($subTotalCommissionOnly * $com)/100, 0).'.00' }}
                                                @else
                                                    0
                                                @endif
                                                </th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                            
                                            </tr> -->
                                            @php
                                            }
                                            @endphp
                                        </tbody>
                                        <tfoot>
                                            @php
                                            $catvaluecommission = 0;
                                            $valueCommission  = DB::table('tbl_order_free_qty_commission')
                                                    ->select( 'tbl_order_free_qty_commission.order_id',DB::raw('SUM(tbl_order_free_qty_commission.total_free_value) AS value'),'tbl_product_category.id AS catid',
                                                    'tbl_product_category.name AS catname','tbl_order_free_qty_commission.fo_id')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_free_qty_commission.catid')
                                                     ->where('tbl_order_free_qty_commission.order_id',$resultInvoice->order_id)
                                                    ->groupBy('tbl_order_free_qty_commission.catid')
                                                    ->get();
                                            

                                            @endphp
                                             @if(sizeof($valueCommission)>0)
                                             @foreach ($valueCommission as $commissionItem)

                                             <tr>
                                                <th colspan="5" align="right">Commission For {{$commissionItem->catname}}</th>
                                                <th style="text-align: right;"> {{ $netAmount = number_format($commissionItem->value,0) }}</th>
                                                <th colspan="5" align="right"></th>
                                            </tr>   

                                            @php
                                            $catvaluecommission += $commissionItem->value;

                                            @endphp

                                             @endforeach   
                                             @endif

                                            <tr>
                                                <th colspan="2" align="right">NET AMOUNT</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;" id="net_amount">
                                                <!-- @if(sizeof($reultProRate)>0)                                                
                                                    {{ $netAmount = number_format($totalPrice - ($totalPrice * $com)/100, 0) }}
                                                @else
                                                    {{ $netAmount = number_format($totalPrice,0) }}
                                                @endif -->
												
												<?php if($totalPrice>$totalDeliveryPrice) { 

                                                 echo $netAmount = number_format($totalPrice-$catvaluecommission,0);
												 $netAmountAct = $totalPrice-$catvaluecommission; 
												
												 } else {
												
												 echo $netAmount = number_format($totalDeliveryPrice-$catvaluecommission,0);
												 $netAmountAct = $totalDeliveryPrice-$catvaluecommission;
                                                
												 } ?>
												
												</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                                                            
                                                <th>&nbsp;</th> 
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>                                          
                                            </tr>

                                            @php
                                            $totalFreeValueWiseCommission =0;
                                            $totalFreeValue =0;
                                            @endphp
                                            @if(sizeof($orderTypePartial)>0 && $orderTypePartial->order_det_status=='Closed')

                                                @if(sizeof($specialValueWise)>0)               
                                                    @php
                                                    foreach($specialValueWise as $svwRows)
                                                    {
                                                        $valueSum = DB::table('tbl_order_special_free_qty')
                                                                ->where('status',3)
                                                                ->where('offer_id',$svwRows->offer_id)
                                                                ->where('order_id',$resultInvoice->order_id)
                                                                ->sum('free_value');

                                                        //echo $valueSum;
                                                        //echo $svwRows->total_free_value;

                                                        //echo $svwRows->offer_id.'_'.$svwRows->total_free_value;
                                                         //$commissionAmount = $svwRows->total_free_value-$valueSum;
                                                       
                                                    @endphp
                                                <tr>
                                                    <th colspan="5"> 
                                                        @php
                                                        $catQuery = DB::table('tbl_special_value_wise_category')
                                                        ->select('tbl_special_value_wise_category.svwid','tbl_special_value_wise_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                        ->leftJoin('tbl_product_category','tbl_special_value_wise_category.categoryid','=','tbl_product_category.id')

                                                        ->where('tbl_special_value_wise_category.svwid', $svwRows->offer_id)
                                                        ->get();

                                                        $commissionAmount = $svwRows->total_free_value  / 100 * $svwRows->commission;

                                                        $totalFreeValue = round($commissionAmount);

                                                        $totalFreeValueWiseCommission = round($commissionAmount - $valueSum);



                                                        foreach($catQuery as $svwcRows)
                                                        {
                                                            echo $catNames = $svwcRows->name.' , ';
                                                            //echo rtrim($catNames,',');
                                                        }


                                                        @endphp
                                                    </th>
                                                    <th colspan="6">{{ round($commissionAmount - $valueSum).' ('.$svwRows->commission.'%'.') ' }} 
                                                        <a href="{{ URL('/order-process-valuewise/'.$resultInvoice->retailer_id.'/'.$resultInvoice->route_id.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'3'.'/'.Request::segment(4)) }}"> &nbsp;&nbsp;
                                                            <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                        </a>
                                                    </th>
                                                </tr>
                                                    @php
                                                    }
                                                    @endphp
                                                @endif
                                            @endif

                                            
                                            @if(sizeof($orderTypePartial)>0 && $orderTypePartial->order_det_status=='Closed')

                                                @if(sizeof($pointValueWise)>0)               
                                                    @php
                                                    foreach($pointValueWise as $svwRows)
                                                    {
                                                        $valueSum = DB::table('tbl_order_special_free_qty')
                                                                ->where('status',3)
                                                                ->where('offer_id',$svwRows->offer_id)
                                                                ->where('order_id',$resultInvoice->order_id)
                                                                ->sum('free_value');

                                                        //echo $valueSum;
                                                        //echo $svwRows->total_free_value;

                                                        //echo $svwRows->offer_id.'_'.$svwRows->total_free_value;
                                                         //$commissionAmount = $svwRows->total_free_value-$valueSum;
                                                       
                                                    @endphp
                                                <tr>
                                                    <th colspan="5"> 
                                                        @php
                                                        $catQuery = DB::table('tbl_point_wise_value_category')
                                                        ->select('tbl_point_wise_value_category.point_value_id','tbl_point_wise_value_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                        ->leftJoin('tbl_product_category','tbl_point_wise_value_category.categoryid','=','tbl_product_category.id')

                                                        ->where('tbl_point_wise_value_category.point_value_id', $svwRows->offer_id)
                                                        ->get();

                                                        $commissionAmount = $svwRows->total_free_value  / 100 * $svwRows->commission;

                                                        $totalFreeValue = round($commissionAmount);

                                                        $totalFreeValueWiseCommission = round($commissionAmount - $valueSum);



                                                        foreach($catQuery as $svwcRows)
                                                        {
                                                            echo $catNames = $svwcRows->name.' , ';
                                                            //echo rtrim($catNames,',');
                                                        }


                                                        @endphp
                                                    </th>
                                                    <th colspan="6">{{ round($commissionAmount - $valueSum).' ('.$svwRows->commission.'%'.') ' }} 
                                                        <a href="{{ URL('/order-process-valuewise/'.$resultInvoice->retailer_id.'/'.$resultInvoice->route_id.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'3'.'/'.Request::segment(4)) }}"> &nbsp;&nbsp;
                                                            <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                        </a>
                                                    </th>
                                                </tr>
                                                    @php
                                                    }
                                                    @endphp
                                                @endif
                                            @endif

                                            @if(sizeof($orderTypePartial)>0 && $orderTypePartial->order_det_status=='Closed')

                                            @if(sizeof($commissionWiseItem)>0)

                                                <tr style="background:#EEEEEE;">
                                                    <th colspan="10"> Exclusive Value Wise Free Items </th>
                                                </tr>
                                                @php
                                                $cPrice =0;
                                                $cqty =0;
                                                @endphp

                                                @foreach($commissionWiseItem as $items)
                                                @php
                                                $cqty +=$items->total_free_qty;
                                                $cPrice +=$items->free_value;
                                                @endphp

                                                <tr>
                                                    <th>&nbsp;</th>  
                                                    <th colspan="2"> {{ $items->name }} </th>            
                                                    <th style="text-align: right;">{{ $items->total_free_qty }}</th>
                                                    <th style="text-align: right;">{{ $items->free_value }}</th>
                                                    <th>&nbsp; </th>                         
                                                    <th>&nbsp; </th>                         
                                                    <th>&nbsp; </th>
                                                    <th colspan="2">
                                                        
                                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editValueWiseCommission('{{ $items->product_id }}','{{ $items->total_free_qty }}','{{ $items->free_value }}','{{ $items->free_id }}')" style="width: 70px;">

                                                
                                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemFreeValueDelete('{{ $items->free_id }}')" style="width: 70px; margin-top: 0px;">
                                                    </th>

                                                <input type="hidden" name="freevalueid" id="freevalueid">                    
                                                                         
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    
                                                    <th colspan="3"> Total Free</th>            
                                                    <th style="text-align: right;">{{ $cqty }}</th>
                                                    <th style="text-align: right;">{{ $cPrice.'.00' }}</th>
                                                    <th>&nbsp; </th>                         
                                                    <th>&nbsp; </th>                         
                                                    <th>&nbsp; </th>
                                                    <th>&nbsp; </th>                         
                                                    <th>&nbsp; </th>                          
                                                </tr>
                                            @endif
                                            @endif


                                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="11" align="right"> Bundle Offer</th>
                                            </tr>

                                            @php
                                            $bs =1;
                                            @endphp

                                            @foreach($resultBundleOffersGift as $gifts)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="2" align="right"> {{ $bs }} </th>
                                                <th colspan="4"> {{ $gifts->name }} </th>
                                                <th style="text-align: right;"> {{ $gifts->stockQty }} </th>
                                                <th style="text-align: right;"> {{ number_format($gifts->stockQty * $gifts->depo,2) }} </th>
                                                <th colspan="4"> </th>     
                                            </tr>
                                            @php
                                            $bs ++;
                                            @endphp
                                            @endforeach

                                            @endif
											
                                        </tfoot>
                                    </table>
                                    <p></p>
                                    <div class="row" style="text-align: center;">
                                             
                                        @if(sizeof($orderTypePartial)>0 && $orderTypePartial->order_det_status=='Closed')
                                              @if($resultInvoice->offer_type=='')
                                                <div class="col-sm-1">
                                                    <input name="offer_type" type="radio" id="radio_8" class="radio-col-red" value="regular" data-toggle="modal" @if($regularOffersCheck >0) checked="" @endif onclick="showRegularProductM()">
                                                    <label for="radio_8"> Regular </label>
                                                </div>

                                                @if(sizeof($specialOffers)>0) 
                                                <div class="col-sm-1">
                                                    <input name="offer_type" type="radio" id="radio_7" class="radio-col-red" value="exclusive" @if($specialOffersCheck >0) checked="" @endif data-toggle="modal" onclick="showSpecialProductM()">
                                                    <label for="radio_7"> Exclusive </label> 
                                                </div>
                                                @endif                                      
                                                
                                                @if(sizeof($resultBundleOffers)>0)
                                                    <div class="col-sm-1">
                                                        <input name="offer_type" type="radio" id="radio_7{{ $resultBundleOffers->iId }}" class="radio-col-red" value="{{ $resultBundleOffers->iId }}" data-toggle="modal" @if($bundleOffersCheck >0) checked="" @endif onclick="showBundleProduct('{{ $resultBundleOffers->iId }}','2')">
                                                        <label for="radio_7{{ $resultBundleOffers->iId }}"> Bundle</label>  
                                                                                             
                                                    </div>
                                                @endif
                                            @else

                                                @if($resultInvoice->offer_type=='Regular')
                                                <div class="col-sm-1">
                                                    <input name="offer_type" type="radio" id="radio_8" class="radio-col-red" value="regular" data-toggle="modal" checked="" onclick="showRegularProductM()">
                                                    <label for="radio_8"> Regular </label>
                                                </div>
                                                @endif

                                                @if($resultInvoice->offer_type=='Exclusive')
                                                @if(sizeof($specialOffers)>0) 
                                                <div class="col-sm-1">
                                                    <input name="offer_type" type="radio" id="radio_7" class="radio-col-red" value="exclusive" @if($specialOffersCheck >0) checked="" @endif data-toggle="modal" onclick="showSpecialProductM()">
                                                    <label for="radio_7"> Exclusive </label> 
                                                </div>
                                                @endif
                                                @endif                                      
                                                
                                                @if($resultInvoice->offer_type=='Bundle')

                                                @if(sizeof($resultBundleOffers)>0)
                                                    <div class="col-sm-1">
                                                        <input name="offer_type" type="radio" id="radio_7{{ $resultBundleOffers->iId }}" class="radio-col-red" value="{{ $resultBundleOffers->iId }}" data-toggle="modal" @if(session('offersSelected')=='bundle') checked="" @endif onclick="showBundleProduct('{{ $resultBundleOffers->iId }}','2')">
                                                        <label for="radio_7{{ $resultBundleOffers->iId }}"> Bundle</label>  
                                                                                             
                                                    </div>
                                                @endif
                                                @endif

                                            @endif
                                        
											
											
                                        @endif 
										 

                                        
                                            
                                            
                                        <div class="col-sm-3">
                                            @if($checkExclusiveAvailableItems > 0 && $checkExclusiveAdded > 0)
                                            <button onclick="statusProcessM(1)" id="confirmDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                            @elseif($checkExclusiveAvailableItems==Null && $checkExclusiveAdded > 0)
                                             <button onclick="statusProcessM(1)" id="confirmDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                              @elseif($checkExclusiveAvailableItems==Null && $checkExclusiveAdded==Null)
                                              <button onclick="statusProcessM(1)" id="confirmDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                               @elseif($resultBundleAddedItems>0)
                                                <button onclick="statusProcessM(1)" id="confirmDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                                @else
                                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" disabled="">Confirm Delivery</button>
                                        
                                            @endif
                                        </div>
                                        

                                        @if(sizeof($orderTypePartial)>0 && $orderTypePartial->order_det_status=='Closed')
                                            <div class="col-sm-3">
                                                 <button onclick="statusProcessM(2)" id="updateDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect" disabled="" >Update Delivery</button> 
                                                <!-- <button onclick="statusProcessM(2)" id="updateDelivery1" type="button" class="btn bg-pink btn-block btn-lg waves-effect" >Update Delivery</button> -->
                                            </div>
                                        @endif
                                         

                                        <!-- <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemDelete()" style="width: 70px; margin-top: 0px;"> -->


                                    </div>
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                                    <input type="hidden" name="retailderid" id="retailderid" value="{{ $resultInvoice->retailer_id }}">
                                    <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                                    <input type="hidden" name="statusProcess" id="statusProcess" value="1">
                                    <input type="hidden" name="orderTypePartial" id="orderTypePartial" value="{{ $orderTypePartial->order_det_status }}">
                                    <input type="hidden" name="openOrderType" id="openOrderType" value="{{ $orderTypePartial->partial_order_id }}">

                                   
                                    <input type="hidden" name="pointid" id="pointid" value="{{ $resultInvoice->point_id }}">
                                    <input type="hidden" name="routeid" id="routeid" value="{{ $resultInvoice->route_id }}">

                                    <input type="hidden" name="totalFreeValue" id="totalFreeValue" value="{{ $totalFreeValue }}">

                                    <input type="hidden" name="totalFreeValueWiseCommissionBalance" id="totalFreeValueWiseCommissionBalance" value="{{ $totalFreeValueWiseCommission }}">
                                    <input type="hidden" name="netAmount" id="netAmount" value="{{ $totalDeliveryPrice }}">
                                    
                            </div>
                                
                            @endif
                        </div>
                    </div>
                </div>

            </form>

            <!-- #END# Basic Validation -->            
        </div>
    </section>

    <div class="modal fade" id="showBundleProductCon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="showBundleProductContent"></div>
    </div>

    <div class="modal fade" id="showBundleProductConMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header" style="background-color: #A62B7F">
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                    <h4 class="modal-title" id="myModalLabel" >Success</h4>
                </div>
            
                <div class="modal-body" style="text-align: center;">
                    {{-- <p><h4>Successfully added offer product</h4></p> --}}
                    <p>Successfully added offer product</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection