@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            BUCKET
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit-exception') }}"> Visit </a> / <a href="{!! URL::previous() !!}"> New Order </a> / Bucket
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
            
            <form action="{{ URL('/confirm-order-exception') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

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
                                                <th>Free Qty</th>
                                                <th>Free Value</th>
                                                <th>Wastage Qty</th>                                            
                                                <th>Wastage Value</th>                                            
                                                <!-- <th>Edit</th>      -->                                       
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            
                                            <tr>
                                                <th colspan="9">{{ $resultRetailer->name }}</th>                  
                                            </tr>

                                            @php
                                                $commissionCategoriesEx  = DB::table('tbl_except_category_commission')
                                                        ->select('categoryId')
                                                        ->where('status',0)
                                                        ->where('global_company_id',Auth::user()->global_company_id)
                                                        ->get();

                                                $data = collect($commissionCategoriesEx)->map(function($x){ return (array) $x; })->toArray(); 

                                                //print_r($data);
                                            @endphp

                                            @if(sizeof($resultCartPro)>0)
                                            @php
                                            $serial   = 1;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalQty = 0;
                                            $totalWastage = 0;
                                            $totalWastageValue = 0;
                                            $totalFreeQty = 0;
                                            $totalFreeQtyValue = 0;
                                            $subTotalCommissionOnly =0;
                                            @endphp
                                            @foreach($resultCartPro as $items)

                                            @php
                                            $valueSum = DB::table('tbl_order_special_free_qty')
                                                        ->where('status',3)
                                                        ->where('subcat_id',$items->cat_id)
                                                        ->sum('free_value');
                                            @endphp

                                            <tr>
                                                <th></th>
                                                @if($items->catid==$items->valuecatid)
                                                {{-- @php
                                                $commissionAmount = $items->total_free_value-$valueSum;
                                                @endphp
                                                
                                                    <th colspan="6">{{ $items->catname }} </th>
                                                    <th colspan="1">{{ $commissionAmount.' ('.$items->commission.'%'.') ' }} 
                                                        <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$items->order_id.'/'.$commissionAmount.'/'.$items->catid) }}">
                                                            <input type="button" name="edit" id="edit" value="ADD" class="btn bg-green btn-block btn-sm waves-effect" style="width: 70px;">
                                                        </a>
                                                    </th>  --}} 
                                                    <th colspan="8">{{ $items->catname }} </th>        
                                                @else
                                                    <th colspan="8">{{ $items->catname }} </th>
                                                @endif                                                
                                            </tr>


                                                @php
                                                
                                                 $resultProComm  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.cat_id',  DB::raw('SUM(tbl_order_details.p_total_price) AS p_total_price'),
													'tbl_product_category.id AS catid',
													
													'tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id',
													'tbl_order.order_type', 'tbl_order.order_status',
													'tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    
													->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                    ->where('tbl_order.order_status','Close_Req')
                                                    ->where('tbl_order.fo_id',Auth::user()->id)
                                                    ->where('tbl_order.retailer_id',$retailderid)
                                                    ->where('tbl_order_details.cat_id', $items->catid)
													->groupBy('tbl_order_details.cat_id')
                                                    ->whereNotIn('tbl_order_details.cat_id', $data)    
                                                    ->get();
                                                   //dd($reultPro);
                                                @endphp
                                                @foreach ($resultProComm as $itemsPro)
                                                    @php
                                                        $subTotalCommissionOnly += $itemsPro->p_total_price;
                                                    @endphp
                                                @endforeach


                                                @php                                                
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id',
													'tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id',
													'tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_product_category.id AS catid',
													
													'tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.offer_type','tbl_order.fo_id',
													'tbl_order.order_status', 'tbl_order.order_type',
													'tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                    ->where('tbl_order.order_status','Close_Req')
                                                    ->where('tbl_order.fo_id',Auth::user()->id)
                                                    ->where('tbl_order.retailer_id',$retailderid)
                                                    ->where('tbl_order.offer_type', Request::segment(6))
                                                    ->where('tbl_order_details.cat_id', $items->catid)
													//->groupBy('tbl_order_details.product_id')													
                                                    ->get();
                                                   //dd($reultPro);
                                                    //echo Request::segment(6);
                                                @endphp
                                                @foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalQty += $itemsPro->order_qty;
                                                $totalWastage += $itemsPro->wastage_qty;
                                                $totalWastageValue += $itemsPro->wastage_qty * $itemsPro->depo;

                                                @endphp

                                                <tr>
                                                <th>{{ $serial }}</th>
                                                <th></th>
                                                <th>{{ $itemsPro->proname }}</th>
                                                <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                                <th style="text-align: right;">{{ number_format($itemsPro->p_total_price,2) }}</th>
                                                <th style="text-align: right;">-</th>  
                                                <th style="text-align: right;">-</th>  
                                                <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty }} @endif</th> 
                                                <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty * $itemsPro->depo }} @endif</th>
                                                                                          
                                                <input type="hidden" id="order_det_id{{ $serial }}" value="{{ $itemsPro->order_det_id }}">
                                                    <input type="hidden" id="pro_id{{ $serial }}" value="{{ $itemsPro->product_id }}">
                                                    <input type="hidden" id="order_id{{ $serial }}" value="{{ $itemsPro->order_id }}">
                                                    <input type="hidden" id="order_qty{{ $serial }}" value="{{ $itemsPro->order_qty }}">
                                                    <input type="hidden" id="p_unit_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}">
                                                    <input type="hidden" id="cat_id{{ $serial }}" value="{{ $itemsPro->cat_id }}">
                                                </tr>
                                                @php
                                                $serial ++;
                                                @endphp
                                                @endforeach

                                                    @php                                             
                                                        $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                        ->select('fq.type','fq.order_id','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname','fq.status','fq.free_id')
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

                                                    // REGULAR OFFER AND OPTION QUERY HERE
                                                        $reultRegularAnd  = DB::table('tbl_order_regular_and_free_qty AS fq')
                                                        ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.special_id','tbl_product.id','tbl_product.name AS proname')
                                                        ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                        // ->where('fq.status','0')      
                                                        ->where('fq.order_id',$resultInvoice->order_id)
                                                        ->where('fq.catid', $items->catid)
                                                        ->where('fq.special_id', $itemsPro->free_id)
                                                        ->first();
                                                    
                                                    @endphp
                                                        <tr>
                                                        <th>{{ $serial }}</th>
                                                        <th></th>
                                                        <th>{{ $itemsPro->proname }} [ FREE ]</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">{{ $itemsPro->total_free_qty }}</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"> </th>     
                                                        <th style="text-align: right;"> </th>       
                                                        <th style="text-align: right;"> </th>
                                                        @endif
                                                        </tr>

                                                        @if(sizeof($reultRegularAnd)>0)
                                                        @php
                                                        $totalFreeQty += $reultRegularAnd->total_free_qty;
                                                        $serial ++;                                                    
                                                        @endphp 
                                                            <tr>
                                                            <th>{{ $serial }}</th>
                                                            <th></th>
                                                            <th>{{ $reultRegularAnd->proname }} [ FREE ]</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">{{ $reultRegularAnd->total_free_qty }}</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            @if(Auth::user()->user_type_id==5)
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;"> </th>     
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
                                                        ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                        ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                        ->where('fq.status','0')      
                                                        ->where('fq.order_id',$resultInvoice->order_id)
                                                        ->where('fq.catid', $items->catid)
                                                        ->get();
                                                    @endphp
                                                    @foreach ($reultRegularGift as $itemsPro)
                                                    @php
                                                    $totalFreeQty += $itemsPro->total_free_qty;
                                                    $totalFreeQtyValue += $itemsPro->total_free_qty*$itemsPro->depo;


                                                        // SPECIAL OFFER AND OPTION QUERY HERE
                                                        $reultSpecialAnd  = DB::table('tbl_order_special_and_free_qty AS fq')
                                                        ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.special_id','tbl_product.id','tbl_product.name AS proname')
                                                        ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                        // ->where('fq.status','0')      
                                                        ->where('fq.order_id',$resultInvoice->order_id)
                                                        ->where('fq.catid', $items->catid)
                                                        ->where('fq.special_id', $itemsPro->free_id)
                                                        ->first();
                                                            
                                                    @endphp

                                                        <tr>
                                                        <th>{{ $serial }}</th>
                                                        <th></th>
                                                        <th>{{ $itemsPro->proname }} [ SP FREE ]</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">{{ $itemsPro->total_free_qty }}</th>
                                                        <th style="text-align: right;"> {{ $itemsPro->total_free_qty*$itemsPro->depo }}</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"> </th>     
                                                        <th style="text-align: right;"> </th>       
                                                        <th style="text-align: right;"> </th>
                                                        @endif
                                                        </tr>

                                                        @if(sizeof($reultSpecialAnd)>0)
                                                        @php
                                                        $totalFreeQty += $reultSpecialAnd->total_free_qty;
                                                        $serial ++;                                                    
                                                        @endphp 
                                                            <tr>
                                                            <th>{{ $serial }}</th>
                                                            <th></th>
                                                            <th>{{ $reultSpecialAnd->proname }} [ SP FREE ]</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">{{ $reultSpecialAnd->total_free_qty }}</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;">-</th>
                                                            @if(Auth::user()->user_type_id==5)
                                                            <th style="text-align: right;">-</th>
                                                            <th style="text-align: right;"> </th>     
                                                            <th style="text-align: right;"> </th>       
                                                            <th style="text-align: right;"> </th>
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
                                            <input type="hidden" id="itemsid" value="">
                                            @php
                                            $catvaluecommission = 0;
                                            $valueCommission  = DB::table('tbl_order_free_qty_commission')
                                                    ->select( 'tbl_order_free_qty_commission.order_id',DB::raw('SUM(tbl_order_free_qty_commission.total_free_value) AS value'),'tbl_product_category.id AS catid',
                                                    'tbl_product_category.name AS catname','tbl_order_free_qty_commission.fo_id')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_free_qty_commission.catid')
                                                     ->where('tbl_order_free_qty_commission.order_id',$resultInvoice->order_id)
                                                    ->where('tbl_order_free_qty_commission.fo_id',Auth::user()->id)
                                                    ->where('tbl_order_free_qty_commission.retailer_id',$retailderid)
                                                    ->groupBy('tbl_order_free_qty_commission.catid')
                                                    ->get();

                                            @endphp
                                             @if(sizeof($valueCommission)>0)
                                             @foreach ($valueCommission as $commissionItem)

                                             <tr>
                                                <th colspan="4" align="right">Commission For {{$commissionItem->catname}}</th>
                                                <th style="text-align: right;"> {{ $netAmount = number_format($commissionItem->value,2) }}</th>
                                                <th colspan="4" align="right"></th>
                                            </tr>   

                                            @php
                                            $catvaluecommission += $commissionItem->value;
                                            @endphp

                                             @endforeach  
                                             @endif


                                            <tr>
                                                <th colspan="3" align="right">Grand Total</th>
                                                <th style="text-align: right;">{{ $totalQty }}</th>
                                                <th style="text-align: right;"> {{ $netAmount = number_format($subTotal-$catvaluecommission,2) }}</th>
                                                <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                                <th style="text-align: right;">{{ $totalFreeQtyValue }}</th>
                                                <th style="text-align: right;">{{ $totalWastage }}</th>
                                                <th style="text-align: right;">{{ $totalWastageValue }}</th>
                                            </tr>                                    
                                            @php

                                            // Memo commission offer

                                            $resultFoInfo   = DB::table('users')
                                                ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                                                 ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                                                 ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                                                 ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                                 ->where('tbl_user_type.user_type_id', 12)
                                                 ->where('users.id', Auth::user()->id)
                                                 ->where('users.is_active', 0) // 0 for active
                                                 ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                                                 ->first();

                                                $point_id       = $resultFoInfo->point_id;
                                                $division_id    = $resultFoInfo->division_id;
                                                $memoOffers = 0;
                                                $point = 0;
                                                $route = 0;
                                                $com = 0;
                                                $comValue = 0;

                                                $businessType = Auth::user()->business_type_id; 
                                                $currentDay = date('Y-m-d');

                                            $memoOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
                                             FROM
                                             tbl_bundle_offer
                                             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
                                             WHERE 
                                             tbl_bundle_offer.iStatus='1'
                                             AND tbl_bundle_offer.iOfferType='4'
                                             AND tbl_bundle_offer.iBusinessType='$businessType'
                                             AND tbl_bundle_offer_scope.iDivId='$division_id' 
                                             AND '$currentDay' BETWEEN dBeginDate 
                                             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
                                             ");

                                            if(sizeof($memoOffers)>0)
                                            {           

                                                $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                                                     FROM tbl_bundle_offer_scope 
                                                     WHERE iOfferId='".$memoOffers[0]->iId."' 
                                                     AND iPointId='$point_id'
                                                     "); 

                                                $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                                                         FROM tbl_bundle_offer_scope 
                                                         WHERE iOfferId='".$memoOffers[0]->iId."' 
                                                         AND iPointId='$point_id' AND iRouteId = '$routeid'
                                                         ");
                                            }





                                        if(sizeof($memoOffers)>0 AND sizeof($point)>0 AND sizeof($route)>0)
                                        {
                                            
                                            $reultProRate  = DB::select("SELECT * FROM tbl_commission WHERE businessType='".Auth::user()->business_type_id."' AND '$subTotalCommissionOnly' BETWEEN minSlab AND maxSlab LIMIT 1");

                                            if(sizeof($reultProRate)>0)
                                            {
                                                $com += $reultProRate[0]->rat;                      
                                            
                                            
                                            @endphp

                                            <!-- <tr>
                                                <th colspan="4" align="left">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                                <th style="text-align: right;">
                                                @if(sizeof($reultProRate)>0)                                                
                                                    {{  $comValue1 = number_format(($subTotalCommissionOnly * $com)/100, 0).'.00' }}
                                                    @php
                                                    $comValue = ($subTotalCommissionOnly * $com)/100;
                                                    @endphp
                                                @else
                                                    0
                                                @endif
                                                </th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                            
                                            </tr> -->

                                            <tr>
                                                <th colspan="4" align="right">NET AMMOUNT</th>
                                                <th style="text-align: right;">
                                                @if(sizeof($reultProRate)>0)                                                
                                                    {{ $netAmount = number_format($subTotal - ($subTotal * $com)/100, 0) }}
                                                @else
                                                    {{ $netAmount = number_format($subTotal,0) }}
                                                @endif   
                                                </th>
                                                <th>&nbsp; </th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                            
                                            </tr>

                                            @php
                                            }
                                        }
                                            @endphp

                                            
                                            </tbody>
                                        <tfoot>

                                            @php
                                            $totalFreeValueWiseCommission =0;
                                            $totalFreeValue =0;
                                            @endphp

                                            @if(sizeof($specialValueWise)>0)               
                                                @php
                                                foreach($specialValueWise as $svwRows)
                                                {
                                                    $valueSum = DB::table('tbl_order_special_free_qty')
                                                            ->where('status',3)
                                                            ->where('offer_id',$svwRows->offer_id)
                                                            ->where('order_id',$resultInvoice->order_id)
                                                            ->sum('free_value');

                                                    //echo $svwRows->offer_id.'_'.$svwRows->total_free_value;

                                                    $commissionAmount = ($svwRows->total_free_value /100 * $svwRows->commission)-$valueSum;
                                                @endphp
                                            <tr>
                                                <th colspan="4"> 
                                                    @php
                                                    $catQuery = DB::table('tbl_special_value_wise_category')
                                                    ->select('tbl_special_value_wise_category.svwid','tbl_special_value_wise_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                    ->leftJoin('tbl_product_category','tbl_special_value_wise_category.categoryid','=','tbl_product_category.id')

                                                    ->where('tbl_special_value_wise_category.svwid', $svwRows->offer_id)
                                                    ->get();

                                                    $commissionAmount = $svwRows->total_free_value  / 100 * $svwRows->commission;

                                                        $totalFreeValue = round($commissionAmount + $prevoiusBalanceCommission->reminding_commission_balance);

                                                        $totalFreeValueWiseCommission = round(($commissionAmount + $prevoiusBalanceCommission->reminding_commission_balance) - ($valueSum));

                                                    foreach($catQuery as $svwcRows)
                                                    {
                                                        echo $catNames = $svwcRows->name.' , ';
                                                        //echo rtrim($catNames,',');
                                                    }
                                                    @endphp
                                                </th>
                                                <th colspan="5">{{ round($commissionAmount - $valueSum).' ('.$svwRows->commission.'%'.') ' }} 
                                                    <a href="{{ URL('/order-process-valuewise-exception/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'1'.'/'.Request::segment(5).'/'.Request::segment(6)) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                    </a>
                                                </th>
                                            </tr>
                                                @php
                                                }
                                                @endphp
                                            @endif

                                            @if(sizeof($pointValueWise)>0)               
                                                @php
                                                foreach($pointValueWise as $svwRows)
                                                {
                                                    $valueSum = DB::table('tbl_order_special_free_qty')
                                                            ->where('status',3)
                                                            ->where('offer_id',$svwRows->offer_id)
                                                            ->where('order_id',$resultInvoice->order_id)
                                                            ->sum('free_value');

                                                    //echo $svwRows->offer_id.'_'.$svwRows->total_free_value;

                                                    $commissionAmount = ($svwRows->total_free_value /100 * $svwRows->commission)-$valueSum;
                                                @endphp
                                            <tr>
                                                <th colspan="4"> 
                                                    @php
                                                    $catQuery = DB::table('tbl_point_wise_value_category')
                                                    ->select('tbl_point_wise_value_category.point_value_id','tbl_point_wise_value_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                    ->leftJoin('tbl_product_category','tbl_point_wise_value_category.categoryid','=','tbl_product_category.id')

                                                    ->where('tbl_point_wise_value_category.point_value_id', $svwRows->offer_id)
                                                    ->get();

                                                    $commissionAmount = $svwRows->total_free_value  / 100 * $svwRows->commission;

                                                        $totalFreeValue = round($commissionAmount + $prevoiusBalanceCommission->reminding_commission_balance);

                                                        $totalFreeValueWiseCommission = round(($commissionAmount + $prevoiusBalanceCommission->reminding_commission_balance) - ($valueSum));

                                                    foreach($catQuery as $svwcRows)
                                                    {
                                                        echo $catNames = $svwcRows->name.' , ';
                                                        //echo rtrim($catNames,',');
                                                    }
                                                    @endphp
                                                </th>
                                                <th colspan="5">{{ round($commissionAmount - $valueSum).' ('.$svwRows->commission.'%'.') ' }} 
                                                    <a href="{{ URL('/order-process-valuewise-exception/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'1'.'/'.Request::segment(5).'/'.Request::segment(6)) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                    </a>
                                                </th>
                                            </tr>
                                                @php
                                                }
                                                @endphp
                                            @endif

                                            @if(sizeof($commissionWiseItem)>0)

                                                <tr style="background:#EEEEEE;">
                                                    <th colspan="9"> Exclusive Value Wise Free Items </th>
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
                                                    
                                                    <th colspan="2">
                                                        
                                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editValueWiseCommission('{{ $items->product_id }}','{{ $items->total_free_qty }}','{{ $items->free_value }}','{{ $items->free_id }}')" style="width: 70px;">

                                                
                                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemFreeValueDelete('{{ $items->free_id }}')" style="width: 70px; margin-top: 0px;">
                                                    </th>
                                                    <th>&nbsp; </th> 

                                                <input type="hidden" name="freevalueid" id="freevalueid">                    
                                                                         
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    
                                                    <th colspan="3"> Total Free</th>            
                                                    <th style="text-align: right;">{{ $cqty }}</th>
                                                    <th style="text-align: right;">{{ $cPrice.'.00' }}</th>
                                                    <th>&nbsp; </th>                         
                                                    <th colspan="3"> </th>                       
                                                </tr>
                                            @endif



                                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="9" align="right"> Bundle Offer</th>
                                            </tr>

                                            @php
                                            $bs =1;
                                            @endphp

                                            @foreach($resultBundleOffersGift as $gifts)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="2" align="right"> {{ $bs }} </th>
                                                <th colspan="3"> {{ $gifts->name }} </th>
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
                                        <input type="hidden" name="offerType" value="{{ Request::segment(6) }}">

                                        @if(Request::segment(6)=='Regular')
                                        <div class="col-sm-1">
    										<input name="offer_type" type="radio" id="radio_8" class="radio-col-red" value="regular" data-toggle="modal" checked="" onclick="showRegularProductM()">
    										<label for="radio_8"> Regular</label>
                                        </div>
                                        @endif
    									
    									
                                        @if(Request::segment(6)=='Exclusive')
                                            @if(sizeof($specialOffers)>0)
                                            <div class="col-sm-1">                                    
                                                
                                                    <input name="offer_type" type="radio" id="radio_7" class="radio-col-red" value="exclusive" data-toggle="modal" checked="" onclick="showSpecialProductM()">
                                                    <label for="radio_7"> Exclusive</label> 
                                            </div>
                                            @endif
                                        @endif
    									
                                        @if(Request::segment(6)=='Bundle')
        									@if(sizeof($resultBundleOffers)>0)
                                            <div class="col-sm-1">
                                                    <input name="offer_type" type="radio" id="radio_7{{ $resultBundleOffers->iId }}" class="radio-col-red" value="{{ $resultBundleOffers->iId }}" data-toggle="modal" checked="" onclick="showBundleProduct('{{ $resultBundleOffers->iId }}','0')">
                                                    <label for="radio_7{{ $resultBundleOffers->iId }}"> Bundle</label>                         
                                            </div>
                                            @endif
                                        @endif                                    


                                @if(Request::segment(6)=='Exclusive')
                                    {{-- Exclusive Offer --}}
                                    @if($checkExclusiveAvailableItems > 0 && $checkExclusiveAdded > 0)
                                                                        
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>

                                    @elseif($checkExclusiveAvailableItems==Null && $checkExclusiveAdded > 0)
                                    	<div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>
                                    @elseif($checkExclusiveAvailableItems==Null && $checkExclusiveAdded==Null)
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>							
                                    @else
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" disabled="">Confirm</button>
                                        </div>
                                    @endif
                                @endif

                                @if(Request::segment(6)=='Regular')
                                    {{-- Regular Offer --}}
                                    @if($checkRegularAvailableItems > 0 && $checkRegularAdded > 0)
                                                                        
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>

                                    @elseif($checkRegularAvailableItems==Null && $checkRegularAdded > 0)
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>
                                    @elseif($checkRegularAvailableItems==Null && $checkRegularAdded==Null)
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
                                        </div>                          
                                    @else
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" disabled="">Confirm</button>
                                        </div>
                                    @endif
                                @endif

                                @if(Request::segment(6)=='Bundle')
                                    {{-- Bundle Offer --}}
                                    @if($resultBundleAvailableItems >0) 
                                        @if($resultBundleAddedItems>0)          
                                            <div class="col-sm-3" style="padding-left: 20px;">
                                                <!-- <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button> -->
												<input type="submit" class="btn bg-pink btn-block btn-lg waves-effect" name="confirm" value="Confirm" /> 
                                            </div>   
                                        @else
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <!-- <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" disabled="">Confirm</button> -->
											<input type="submit" class="btn bg-pink btn-block btn-lg waves-effect"  name="confirm" value="Confirm" disabled/> 
                                        </div>
                                        @endif
                                    @else
                                        <div class="col-sm-3" style="padding-left: 20px;">
                                            <!-- <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm 1</button> -->
											 <input type="submit" class="btn bg-pink btn-block btn-lg waves-effect" name="confirm" value="Confirm"  /> 
                                        </div>
                                    @endif
                                @endif
									
                                    
									<input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                                    <input type="hidden" name="retailderid" id="retailderid" value="{{ $retailderid }}">
                                    <input type="hidden" name="orderpid" id="orderpid" value="{{ $resultInvoice->order_no }}">
                                    <input type="hidden" name="routeid" id="routeid" value="{{ $routeid }}">
                                    <input type="hidden" name="pointid" id="pointid" value="{{ $pointid }}">
                                    <input type="hidden" name="distributorID" id="distributorID" value="{{ $distributorID }}">
                                    <input type="hidden" name="type" id="type" value="1">
                                    
									<input type="hidden" name="offer_confirm" id="offer_confirm" value="YES">

                                    <input type="hidden" name="memo_commission_rat" id="memo_commission_rat" value="{{ $com }}">
                                            <input type="hidden" name="memo_commission_value" id="memo_commission_value" value="{{ $comValue }}">
                                    <input type="hidden" name="netAmount" id="netAmount" value="{{ $subTotal }}">

                                    <input type="hidden" name="totalFreeValue" id="totalFreeValue" value="{{ $totalFreeValue }}">

                                    <input type="hidden" name="totalFreeValueWiseCommissionBalance" id="totalFreeValueWiseCommissionBalance" value="{{ $totalFreeValueWiseCommission }}">

                                    <input type="hidden" name="partial_id" id="partial_id" value="{{ Request::segment(5) }}">


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
                                            <a href="{{ URL('/order-process-exception/'.$retailderid.'/'.$routeid) }}">
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