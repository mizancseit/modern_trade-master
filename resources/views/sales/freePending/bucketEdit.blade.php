@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            FREE PENDING DELIVERY ENTRY
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Free Pending
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
            
            <form action="{{ URL('/free-pending-edit-submit') }}" name="form" id="form" method="POST">
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
                                <h2>FREE PENDING DELIVERY ENTRY</h2>                            
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
                                                <th>Change Cat</th>
                                                <th>Change Products</th>
                                                <th>Free Pending</th>
                                                <th>F.Value</th>                                           
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
                                            $serial   = 0;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalQty = 0;
                                            $totalWastage = 0;
                                            $totalPrice = 0;
                                            $totalDeliveryPrice = 0;
                                            $totalFreeQty =0;
                                            $totalFreeValue =0;
                                            $totalFreeDeliveryQty =0;
                                            $totalFreeDeliveryValue =0;
                                            $subTotalCommissionOnly =0;
                                            @endphp
                                            @foreach($resultCartPro as $items)                                       
                                            <tr>
                                                <th colspan="11">{{ $items->catname }}</th>
                                            </tr>

                                                @php
                                                $serial ++;
                                               
                                                                                       
                                                    $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                    ->select('fq.type','fq.status','fq.order_id','fq.auto_order_no','fq.catid','fq.product_id','fq.slab','fq.total_free_qty','fq.free_value','fq.free_delivery_qty','fq.free_delivery_value','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo','fq.free_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.type','R')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->where('fq.status', 0)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->free_delivery_qty;
                                                $totalFreeDeliveryQty +=$itemsPro->total_free_qty;
                                                $totalFreeValue += $itemsPro->free_delivery_value;
                                                $totalFreeDeliveryValue +=$itemsPro->free_value;
                                                
                                                // REGULAR OFFER AND OPTION QUERY HERE
                                                $reultRegularAnd  = DB::table('tbl_order_regular_and_free_qty AS fq')
                                                ->select('fq.order_id','fq.catid','fq.product_id','fq.slab','fq.total_free_qty','fq.free_value','fq.free_delivery_qty','fq.free_delivery_value','fq.special_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
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
                                                    <input type="number" name="freeqty[]" id="freeqty{{ $serial }}" value="@if($itemsPro->total_free_qty=='') {{ $itemsPro->total_free_qty}}@else{{$itemsPro->free_delivery_qty}}@endif" class="form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off">
                                                    <input type="hidden" name="offer_type" id="offer_type" value="regular">
                                                    <input type="hidden" name="slab" id="slab" value="{{$itemsPro->slab}}">
                                                    </th>
                                                    <th style="text-align: right;">
                                                        <input type="hidden" name="total_free_value[]" id="total_free_value{{ $serial }}" value="{{ $itemsPro->free_value }}" >
                                                        {{ $itemsPro->free_value }}</th>
                                                    
                                                  
                                                    </tr>


                                                    @if(sizeof($reultRegularAnd)>0)
                                                        @php
                                                        $totalFreeQty += $itemsPro->free_delivery_qty;
                                                        $totalFreeDeliveryQty +=$itemsPro->total_free_qty;
                                                        $totalFreeValue += $itemsPro->free_delivery_value;
                                                        $totalFreeDeliveryValue +=$itemsPro->free_value;
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
                                                                <input type="number" name="andFreeQty[]" id="andFreeQty{{ $serial }}" value="@if($reultRegularAnd->total_free_qty==''){{ $reultRegularAnd->total_free_qty}}@else {{ $reultRegularAnd->free_delivery_qty}}@endif" class="form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off">
                                                            </th>
                                                            <th style="text-align: right;">
                                                        <input type="hidden" name="total_and_free_value[]" id="total_and_free_value{{ $serial }}" value="{{ $reultRegularAnd->free_value }}" >
                                                        {{ $reultRegularAnd->free_value }}</th>
                                                           
                                                            </tr>
                                                        @endif

                                                @php
                                                $serial ++;
                                                @endphp                       
                                                @endforeach

                                                {{-- SPECIAL OFFER --}}    
                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_special_free_qty AS fq')
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.slab','fq.total_free_qty','fq.free_value','fq.free_delivery_qty','fq.free_delivery_value','fq.free_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.status','0')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->free_delivery_qty;
                                                $totalFreeDeliveryQty +=$itemsPro->total_free_qty;
                                                $totalFreeValue += $itemsPro->free_delivery_value;
                                                $totalFreeDeliveryValue +=$itemsPro->free_value;
                                                
                                                // SPECIAL OFFER AND OPTION QUERY HERE
                                                $reultSpecialAnd  = DB::table('tbl_order_special_and_free_qty AS fq')
                                                ->select('fq.free_id','fq.order_id','fq.catid','fq.product_id','fq.slab','fq.total_free_qty','fq.free_value','fq.free_delivery_qty','fq.free_delivery_value','fq.special_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                // ->where('fq.status','0')      
                                                ->where('fq.order_id',$resultInvoice->order_id)
                                                ->where('fq.catid', $items->catid)
                                                ->where('fq.special_id', $itemsPro->free_id)
                                                ->first();
                                                 //echo $items->catid;  
                                                @endphp

                                                @if($itemsPro->total_free_qty<$itemsPro->free_delivery_qty)
                                                    <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th>{{ $itemsPro->proname }} [ SP FREE ]</th>
                                                    <th style="text-align: right;">{{$itemsPro->free_delivery_qty}}</th>
                                                    <th style="text-align: right;">{{$itemsPro->free_delivery_qty * $itemsPro->depo}}</th>
                                                    <th style="text-align: right;">{{$itemsPro->total_free_qty}}</th>
                                                    <th style="text-align: right;">{{$itemsPro->total_free_qty * $itemsPro->depo}}</th>   
                                                   <th>
                                                        <div class="form-line" style="width: 170px;">
                                                            <select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">

                                                                @foreach($resultCategory as $pname) 
                                                                <option value="">Select Category</option>                
                                                                <option value="{{ $pname->id }}" @if($pname->id==$itemsPro->catid) selected="" @endif> {{ $pname->name }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>  

                                                    </th>

                                                    <th>
                                                        @php
                                                        $depo_price = 0;

                                                        $resultProduct = DB::table('tbl_product')
                                                        ->select('id','name','depo')
                                                        ->where('status', '0')
                                                        ->where('id', $itemsPro->product_id)
                                                        ->get();

                                                        @endphp

                                                        <div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
                                                            <select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
                                                                <option value="">Select Product</option>
                                                                <?php   foreach($resultProduct as $pname) {
                                                                    if($itemsPro->product_id ==  $pname->id) { ?>
                                                                    <option value="{{ $pname->id }}" selected="">{{ $pname->name }}</option>

                                                                    <?php   

                                                                    if($pname->depo!=''):
                                                                        $depo_price = $pname->depo;
                                                                    else:
                                                                        $depo_price = 0;
                                                                    endif;      

                                                                }   else { ?>
                                                                <option value="{{ $pname->id }}">{{ $pname->name }}</option>
                                                            <?php   } 
                                                        } ?>
                                                    </select>
                                                    <input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="{{$depo_price}}">
                                                    </div>  

                                                    </th>
                                                    <th style="text-align: right;">
                                                        <input type="hidden" name="free_id[]" id="free_id{{ $serial }}" value="{{ $itemsPro->free_id }}" >
                                                        <input type="hidden" name="total_free_qty[]" id="total_free_qty{{ $serial }}" value="{{ $itemsPro->total_free_qty }}" >
                                                     <input type="hidden" name="free_product_id[]" id="free_product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >
                                                     <input type="hidden" name="free_value[]" id="free_value{{ $serial }}" value="{{ $itemsPro->depo }}" >
                                                    {{-- <input type="number" name="freeqty[]" id="freeqty{{ $serial }}" value="{{$itemsPro->free_delivery_qty-$itemsPro->total_free_qty}}" class="form-control" style="width: 70px;" onkeyup="freeqty({{ $serial }})" min="1" autocomplete="off"> --}}

                                                    <input type="number" class="form-control" id="changeQty{{$serial}}" name="freeqty[]" value="{{$itemsPro->free_delivery_qty-$itemsPro->total_free_qty}}" maxlength="3" style="width: 60px;" onkeyup="addChange({{$serial}})">
                                                    
                                                     <input type="hidden" name="offer_type" id="offer_type" value="exclusive">
                                                     <input type="hidden" name="slab[]" id="slab" value="{{$itemsPro->slab}}">
                                                     <input type="hidden" name="catid[]" id="catid" value="{{$itemsPro->catid}}">
                                                    </th>

                                                                                     

                                                    <th>
                                                        <input type="text" class="form-control" id="changeValue{{$serial}}" name="change_value[]" value="{{$itemsPro->free_delivery_value-$itemsPro->free_value}}" maxlength="3" style="width: 70px;"  readonly="">
                                                    </th> 
                                                   </tr>

                                                    @endif

                                                    @if(sizeof($reultSpecialAnd)>0)
                                                    @php
                                                    $totalFreeQty += $itemsPro->free_delivery_qty;
                                                    $totalFreeDeliveryQty +=$itemsPro->total_free_qty;
                                                    $totalFreeValue += $itemsPro->free_delivery_value;
                                                    $totalFreeDeliveryValue +=$itemsPro->free_value;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <th>{{ $serial }}</th>
                                                        <th>{{ $reultSpecialAnd->proname }} [ SP FREE ]</th>

                                                        <th style="text-align: right;">{{$reultSpecialAnd->free_delivery_qty}}</th>
                                                    <th style="text-align: right;">{{$reultSpecialAnd->free_delivery_qty * $reultSpecialAnd->depo}}</th>
                                                    <th style="text-align: right;">{{$reultSpecialAnd->total_free_qty}}</th>
                                                    <th style="text-align: right;">{{$reultSpecialAnd->total_free_qty * $reultSpecialAnd->depo}}</th>   
                                                   <th>
                                                        <div class="form-line" style="width: 170px;">
                                                            <select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
                                                                @foreach($resultCategory as $pname) 
                                                                                                
                                                                <option value="{{ $pname->id }}" @if($pname->id==$reultSpecialAnd->catid) selected="" @endif> {{ $pname->name }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>  

                                                    </th>

                                                    <th>
                                                        @php
                                                        $depo_price = 0;

                                                        $resultProduct = DB::table('tbl_product')
                                                        ->select('id','name','depo')
                                                        ->where('status', '0')
                                                        ->where('id', $reultSpecialAnd->product_id)
                                                        ->get();

                                                        @endphp

                                                        <div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
                                                            <select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
                                                                <option value="">Select Product</option>
                                                                <?php   foreach($resultProduct as $pname) {
                                                                    if($reultSpecialAnd->product_id ==  $pname->id) { ?>
                                                                    <option value="{{ $pname->id }}" selected="">{{ $pname->name }}</option>

                                                                    <?php   

                                                                    if($pname->depo!=''):
                                                                        $depo_price = $pname->depo;
                                                                    else:
                                                                        $depo_price = 0;
                                                                    endif;      

                                                                }   else { ?>
                                                                <option value="{{ $pname->id }}">{{ $pname->name }}</option>
                                                            <?php   } 
                                                        } ?>
                                                    </select>
                                                    <input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="{{$depo_price}}">
                                                    </div>  

                                                    </th>



                                                        <th style="text-align: right;">
                                                            <input type="hidden" name="and_free_id[]" id="and_free_id{{ $serial }}" value="{{ $reultSpecialAnd->free_id }}" >
                                                            <input type="hidden" name="and_free_qty[]" id="and_free_qty{{ $serial }}" value="{{ $reultSpecialAnd->total_free_qty }}" >
                                                             <input type="hidden" name="and_free_pid[]" id="and_free_pid{{ $serial }}" value="{{ $reultSpecialAnd->product_id }}" >
                                                             <input type="hidden" name="and_free_value[]" id="and_free_value{{ $serial }}" value="{{ $reultSpecialAnd->depo }}" >
                                                             <input type="hidden" name="catid[]" id="catid" value="{{$itemsPro->catid}}">
                                                     <input type="number" class="form-control" id="changeQty{{$serial}}" name="andFreeQty[]" value="{{$reultSpecialAnd->free_delivery_qty - $reultSpecialAnd->total_free_qty}}" maxlength="3" style="width: 60px;" onkeyup="addChange({{$serial}})">
                                                    </th>


                                                    <th>
                                                        <input type="text" class="form-control" id="changeValue{{$serial}}" name="total_and_free_value[]" value="{{ $reultSpecialAnd->free_delivery_value - $reultSpecialAnd->free_value }}" maxlength="3" style="width: 70px;"  readonly="">
                                                    </th> 


                                                   {{-- <th style="text-align: right;">
                                                        <input  class="form-control" type="text" name="total_and_free_value[]" id="total_and_free_value{{ $serial }}" style="width: 70px;" value="{{ $reultSpecialAnd->free_delivery_value - $reultSpecialAnd->free_value }}" ></th>
                                                         --}}
                                                      
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
                                                <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalFreeValue,0) }}</th>
                                                
                                                <th style="text-align: right;" id="totalQty">{{ $totalFreeDeliveryQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <th style="text-align: right;" id="totalPrice">{{ number_format($totalFreeDeliveryValue,0) }}
                                                    <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                                                </th>
                                                <th></th>
                                                <th></th>
                                                <th style="text-align: left;">{{ $totalFreeQty-$totalFreeDeliveryQty }}</th>
                                                <th style="text-align: right;">{{ $totalFreeValue-$totalFreeDeliveryValue }}</th>
                                                                                        
                                            </tr>
                                            
                                           
                                            
                                        </tfoot>
                                    </table>
                                    <p></p>
                                    <div class="row" style="text-align: center;">
                                             
                                        <div class="col-sm-3">
                                            <button onclick="statusProcessM(1)" id="confirmDelivery" type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                        </div>
                                       
                                    </div>
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                                    <input type="hidden" name="retailderid" id="retailderid" value="{{ $resultInvoice->retailer_id }}">
                                    <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                                    <input type="hidden" name="statusProcess" id="statusProcess" value="1">

                                   
                                    <input type="hidden" name="pointid" id="pointid" value="{{ $resultInvoice->point_id }}">
                                    <input type="hidden" name="routeid" id="routeid" value="{{ $resultInvoice->route_id }}">

                                    <input type="hidden" name="totalFreeValue" id="totalFreeValue" value="{{ $totalFreeValue }}">

                                    
                                    
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