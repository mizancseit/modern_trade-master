@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            BUCKET EDIT
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/order-manage') }}"> Order Manage </a> / Bucket Edit
                            </small>
                        </h2>
                    </div>

                    <div class="col-lg-3">
                        <a href="{{ URL('/invoice-edit/'.$resultInvoice->order_id.'/'.$retailderid.'/'.$routeid) }}">
                            <button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                        </a>                        
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

                        @if(sizeof($resultCartPro)>0)
                        <div class="header">
                            <h2>ALL BUCKET PRODUCT</h2>                            
                        </div>
                        <div class="body">
                            <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Order Qty</th>
                                            <th>Value</th>
                                            <th>Free</th>
                                            <th>Wastage Qty</th>                                            
                                            <th>Edit</th>                                            
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                        <tr>
                                            <th colspan="8">{{ $resultRetailer->name }}</th>                  
                                        </tr>

                                        @if(sizeof($resultCartPro)>0)
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalWastage = 0;
                                        $totalFreeQty = 0;
                                        @endphp
                                        @foreach($resultCartPro as $items)                                       
                                        <tr>
                                            <th></th>
                                            <th colspan="9">{{ $items->catname }}</th>
                                        </tr>


                                            @php 
                                            $itemsCount = 1;
                                    $reultPro  = DB::table('tbl_order_details')
                                                ->select('tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_order.global_company_id','tbl_product.id','tbl_product.name AS proname')
                                                ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                ->where('tbl_order.order_type','Confirmed')
                                                ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                                                ->where('tbl_order.fo_id',Auth::user()->id)                        
                                                ->where('tbl_order.retailer_id',$retailderid)
                                                ->where('tbl_order_details.cat_id', $items->catid)    
                                                ->get();
                                               //dd($reultPro);
                                            @endphp
                                            @foreach ($reultPro as $itemsPro)
                                            @php
                                            $subTotal += $itemsPro->p_total_price;
                                            $totalQty += $itemsPro->order_qty;
                                            $totalWastage += $itemsPro->wastage_qty;

                                            @endphp

                                            <tr>
                                            <th>{{ $serial }}</th>
                                            <th></th>
                                            <th>{{ $itemsPro->proname }}</th>
                                            <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                            <th style="text-align: right;">{{ number_format($itemsPro->p_total_price,2) }}</th>
                                            <th style="text-align: right;">-</th>
                                            <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty }} @endif</th>                                            
                                            <th>
                                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editProducts('{{ $itemsPro->order_det_id }}','{{ $pointid }}','{{ $routeid }}','{{ $retailderid }}','{{ $items->catid }}')" style="width: 70px;">

                                                
                                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemDelete('{{ $serial }}')" style="width: 70px; margin-top: 0px;">

                                                <input type="hidden" id="order_det_id{{ $serial }}" value="{{ $itemsPro->order_det_id }}">
                                                <input type="hidden" id="pro_id{{ $serial }}" value="{{ $itemsPro->product_id }}">
                                                <input type="hidden" id="order_id{{ $serial }}" value="{{ $itemsPro->order_id }}">
                                                <input type="hidden" id="order_qty{{ $serial }}" value="{{ $itemsPro->order_qty }}">
                                                <input type="hidden" id="p_unit_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}">
                                                <input type="hidden" id="cat_id{{ $serial }}" value="{{ $itemsPro->cat_id }}">

                                            </th>
                                            </tr>
                                            @php
                                            $serial ++;
                                            @endphp
                                            @endforeach

                                            @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                    ->select('fq.type','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.type','R')      
                                                    ->where('fq.auto_order_no',$resultInvoice->auto_order_no)
                                                    ->where('fq.catid', $items->catid)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->total_free_qty;
                                                
                                                @endphp
                                                    <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th></th>
                                                    <th>{{ $itemsPro->proname }} [ FREE ]</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">{{ $itemsPro->total_free_qty }}</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"></th>
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"> </th>     
                                                    <th style="text-align: right;"> </th>       
                                                    <th style="text-align: right;"> </th>
                                                    @endif
                                                    </tr>
                                                @php
                                                $serial ++;
                                                @endphp                       
                                                @endforeach                                          
                                        </tr>
                                        
                                        @endforeach
                                        @endif
                                        <input type="hidden" id="itemsid" value="">
                                        <tr>
                                            <th colspan="3" align="right">Sub Total</th>
                                            <th style="text-align: right;">{{ $totalQty }}</th>
                                            <th style="text-align: right;">{{ number_format($subTotal,2) }}</th>
                                            <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                            <th style="text-align: right;">{{ $totalWastage }}</th>                                            
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                        {{-- <tr>
                                            <th colspan="3" align="right">Grand Total</th>
                                            <th>{{ $totalQty }}</th>
                                            <th>{{ $subTotal }}</th>
                                            <th>{{ $totalWastage }}</th>                                            
                                        </tr> --}}
                                        @php
                                        $com = 0;
                                        $reultProRate  = DB::select("SELECT * FROM tbl_ims_offer_slab WHERE group_id='".Auth::user()->business_type_id."' AND '$subTotal' BETWEEN min_value AND max_value LIMIT 1");

                                        if(sizeof($reultProRate)>0)
                                        {
                                            $com += $reultProRate[0]->rate;                      
                                        }
                                        
                                        @endphp
                                        <tr>
                                            <th colspan="4" align="left">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                            <th style="text-align: right;">
                                            @if(sizeof($reultProRate)>0)                                                
                                                {{ number_format(($subTotal * $com)/100, 0).'.00' }}
                                            @else
                                                0
                                            @endif
                                            </th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>                                            
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" align="right">NET AMMOUNT</th>
                                            <th style="text-align: right;">
                                            @if(sizeof($reultProRate)>0)                                                
                                                {{ $netAmount = number_format($subTotal - ($subTotal * $com)/100, 0).'.00' }}
                                            @else
                                                {{ $netAmount = number_format($subTotal,2) }}
                                            @endif   
                                            </th>
                                            <th>&nbsp; <input type="hidden" name="netAmount" id="netAmount" value="{{ $netAmount }}"> </th>
                                            <th>&nbsp;</th>                                            
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                        @if(sizeof($resultBundleOffersGift)>0)
                                        <tr style="background-color: #EEEEEE">
                                            <th colspan="2" align="right"> {{$resultBundleOffersGift->vOfferName}}</th>
                                            <th colspan="6"> @if($resultBundleOffersGift->productType==2) {{ $resultBundleOffersGift->giftName }} @else {{ $resultBundleOffersGift->name }} @endif </th>     
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>

                                <div class="row">
                                    @if(sizeof($resultBundleOffers)>0)
                                    <div class="col-sm-2">
                                        
                                        @foreach($resultBundleOffers as $offers)
                                            <input name="type" type="radio" id="radio_7{{ $offers->iId }}" class="radio-col-red" value="{{ $offers->iId }}" data-toggle="modal" onclick="showBundleProduct('{{ $offers->iId }}','1')">
                                            <label for="radio_7{{ $offers->iId }}"> {{-- {{ $offers->vOfferName }} --}} Bundle</label>  
                                            
                                        @endforeach                                        
                                    </div>
                                    @endif

                                    <div class="col-sm-2">                                    
                                        <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-delete">Delete</button>
                                    </div>
                                </div>
                                <p></p>
                                {{-- <p></p>
                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-3">
                                    <a href="{{ URL('/confirm-order/'.$resultInvoice->order_id.'/'.$resultInvoice->order_no.'/'.$retailderid.'/'.$routeid.'/'.$pointid.'/'.$distributorID) }}">
                                        <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Order Request</button>
                                    </a>
                                    </div>
                                    <div class="col-sm-2">                                    
                                        <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-delete">Delete</button>
                                    </div>
                                </div> --}}
                                <div class="row" style="text-align: center;">
                                
                            </div>
                                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                                <input type="hidden" name="retailderid" id="retailderid" value="{{ $retailderid }}">
                                <input type="hidden" name="routeid" id="routeid" value="{{ $routeid }}">
                                <input type="hidden" name="type" id="type" value="2">
                        </div>
                        @else
                        
                        <div class="header">
                            <h2>BUCKET EMPTY</h2>                            
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </thead>
                            <tbody>                                        
                                <tr>
                                    <th colspan="7" style="color: #000; text-align: center;" align="center">
                                    <h4>YOUR BUCKET PRODUCT IS EMPTY.</h4> <p></p><p></p>

                                    <div class="col-sm-4" style="margin-right: 40px;"></div>
                                    <div class="col-sm-3">
                                        <a href="{{ URL('/order-process/'.$retailderid.'/'.$routeid) }}">
                                            <button type="button" class="btn bg-red btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                                        </a>
                                    </div> 
                                    
                                     </th>                  
                                </tr>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </tbody>
                        </table>
                            
                        @endif
                    </div>
                </div>
            </div>
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

    <div class="modal fade" id="item-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header" style="background-color: #A62B7F">
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                    <h4 class="modal-title" id="myModalLabel" >Item Delete</h4>
                </div>
            
                <div class="modal-body" style="text-align: center;">
                    <p><h4>Are you sure?</h4></p>
                    <p>You will not be able to recover this imaginary file!</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger btn-ok" onclick="deleteProducts()">Yes</button>
                </div>
            </div>
        </div>
    </div>
@endsection