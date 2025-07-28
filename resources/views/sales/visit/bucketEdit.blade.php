@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>
                            BUCKET EDIT
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/order-manage') }}"> Order Manage </a> / Bucket Edit
                            </small>
                        </h2>
                    </div>

                    <div class="col-lg-3">
                        <a href="{{ URL('/invoice-edit/'.$resultInvoice->order_id.'/'.$retailderid.'/'.$routeid.'/'.$partial_id) }}">
                            <button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                        </a>                        
                    </div>
					
					<?php 
					
					if($resultCartPro[0]->order_status != 'Closed') {
					
					?>
					
					<div class="col-sm-3">
						 <a href="{{ URL('/close-order-manage/'.$resultInvoice->order_id.'/'.Request::segment(6)) }}"> 
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
                                            <th>Free Value</th>
                                            <th>Wastage Qty</th>                                            
                                            <th>Wastage Value</th>                                            
                                            <th>Edit</th>                                            
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                        <tr>
                                            <th colspan="10">{{ $resultRetailer->name }}</th>                  
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
                                        <tr>
                                            <th></th>
                                            <th colspan="10">{{ $items->catname }}</th>
                                        </tr>

                                             {{-- for commission only --}}
                                            @php                                                 
                                             $resultProComm  = DB::table('tbl_order_details')
                                                ->select('tbl_order_details.cat_id','tbl_order_details.p_total_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname',
												'tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                ->where('tbl_order_details.order_det_type','Confirmed')                        
                                                ->where('tbl_order.fo_id',Auth::user()->id)                        
                                                ->where('tbl_order.retailer_id',$retailderid)
                                                ->where('tbl_order_details.cat_id', $items->catid)
												->where('tbl_order_details.partial_order_id', $partial_id)
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
												'tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type',
												'tbl_order.retailer_id','tbl_order.global_company_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                ->where('tbl_order_details.order_det_type','Confirmed')
                                                ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                                                ->where('tbl_order.fo_id',Auth::user()->id)
                                                ->where('tbl_order.order_id',Request::segment(2))
                                                ->where('tbl_order.retailer_id',$retailderid)
                                                ->where('tbl_order_details.cat_id', $items->catid)
												->where('tbl_order_details.partial_order_id', $partial_id) 												
                                                ->get();
                                               //dd($reultPro);
                                            @endphp
                                            @foreach ($reultPro as $itemsPro)
                                            @php
                                            $subTotal += $itemsPro->p_total_price;
                                            $totalQty += $itemsPro->order_qty;
                                            $totalWastage += $itemsPro->wastage_qty;
                                            $totalWastageValue += $itemsPro->wastage_qty*$itemsPro->depo;

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
                                            <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty*$itemsPro->depo }} @endif</th>                                              
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
                                                    ->select('fq.type','fq.auto_order_no','fq.catid','fq.status','fq.product_id','fq.total_free_qty','fq.total_free_value','tbl_product.id','tbl_product.name AS proname','fq.free_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.type','R')      
                                                    ->where('fq.auto_order_no',$resultInvoice->auto_order_no)
                                                    ->where('fq.catid', $items->catid)
                                                    ->where('fq.status', 0)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->total_free_qty;
                                                $totalFreeQtyValue += $itemsPro->total_free_value;
                                                
                                                // REGULAR OFFER AND OPTION QUERY HERE
                                                $reultRegularAnd  = DB::table('tbl_order_regular_and_free_qty AS fq')
                                                ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.total_free_value','fq.special_id','tbl_product.id','tbl_product.name AS proname')
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
                                                    <th style="text-align: right;">{{ number_format($itemsPro->total_free_value,2) }}</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"></th>
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
                                                    $totalFreeQtyValue += $reultRegularAnd->total_free_value;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <th>{{ $serial }}</th>
                                                        <th></th>
                                                        <th>{{ $reultRegularAnd->proname }} [ FREE ]</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">{{ $reultRegularAnd->total_free_qty }}</th>
                                                        <th style="text-align: right;">{{ number_format($reultRegularAnd->total_free_value,2) }}</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"></th>
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
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.free_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.status','0')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                
                                                
                                                // SPECIAL OFFER AND OPTION QUERY HERE
                                                    $reultSpecialAnd  = DB::table('tbl_order_special_and_free_qty AS fq')
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.special_id','tbl_product.id','tbl_product.name AS proname','tbl_product.depo')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    // ->where('fq.status','0')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->where('fq.special_id', $itemsPro->free_id)
                                                    ->first();
                                                     
                                                $totalFreeQty += $itemsPro->total_free_qty;   
                                                $totalFreeQtyValue += $itemsPro->total_free_qty*$itemsPro->depo;   
                                                @endphp
                                                    <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th></th>
                                                    <th>{{ $itemsPro->proname }} [ SP FREE ]</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">{{ $itemsPro->total_free_qty }}</th>
                                                    <th style="text-align: right;">{{ number_format($itemsPro->total_free_qty * $itemsPro->depo,2) }}</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;"> </th>
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
                                                        <th style="text-align: right;">{{ number_format($reultSpecialAnd->total_free_qty * $itemsPro->depo,2) }}</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"> </th>
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
                                        <tr>
                                            <th colspan="3" align="right">Grand Total</th>
                                            <th style="text-align: right;">{{ $totalQty }}</th>
                                            <th style="text-align: right;">{{ number_format($subTotal,2) }}</th>
                                            <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                            <th style="text-align: right;">{{ number_format($totalFreeQtyValue,2) }}</th>
                                            <th style="text-align: right;">{{ $totalWastage }}</th>
                                            <th style="text-align: right;">{{ $totalWastageValue }}</th>
                                            <th>&nbsp;</th>                                            
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

                                            <tr>
                                                <th colspan="4" align="left">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                                <th style="text-align: right;">
                                                @if(sizeof($reultProRate)>0)                                                
                                                    {{  $comValue = number_format(($subTotalCommissionOnly * $com)/100, 0).'.00' }}
                                                @else
                                                    0
                                                @endif
                                                </th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
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
                                            <th>&nbsp; </th>
                                            <th>&nbsp;</th>                                            
                                            <th>&nbsp;</th>                                            
                                            <th>&nbsp;</th>                                            
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                        
                                        @php

                                            }
                                        }
                                        /// End memo commission
                                        
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

                                                    //dd($valueSum);

                                                    $commissionAmount = $svwRows->total_free_value-$valueSum;
                                                @endphp
                                            
                                                <th colspan="4"> 
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
                                                    <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'2'.'/'.Request::segment(6)) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                    </a>
                                                </th>
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

                                                    //dd($valueSum);

                                                    $commissionAmount = $svwRows->total_free_value-$valueSum;
                                                @endphp
                                            
                                                <th colspan="4"> 
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
                                                <th colspan="4">{{ round($commissionAmount - $valueSum).' ('.$svwRows->commission.'%'.') ' }}
                                                    <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$totalFreeValueWiseCommission.'/'.$items->catid.'/'.$svwRows->offer_id.'/'.'2'.'/'.Request::segment(6)) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD FREE ITEMS" class="btn bg-green btn-block btn-sm waves-effect button" style="width: 150px;">
                                                    </a>
                                                </th>
                                                @php
                                                }
                                                @endphp
                                            @endif

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
                                                    <th colspan="2">
                                                        
                                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editValueWiseCommission('{{ $items->product_id }}','{{ $items->total_free_qty }}','{{ $items->free_value }}','{{ $items->free_id }}')" style="width: 70px;">

                                                
                                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemFreeValueDelete('{{ $items->free_id }}')" style="width: 70px; margin-top: 0px;">
                                                    </th>
                                                    <th>&nbsp; </th> 
                                                    <th>&nbsp; </th> 

                                                <input type="hidden" name="freevalueid" id="freevalueid">                    
                                                                         
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    
                                                    <th colspan="3"> Total Free</th>            
                                                    <th style="text-align: right;">{{ $cqty }}</th>
                                                    <th style="text-align: right;">{{ $cPrice.'.00' }}</th>
                                                    <th>&nbsp; </th>  
                                                    <th colspan="4"></th>                      
                                                </tr>
                                            @endif
                                        
                                        @if(sizeof($resultBundleOffersGift)>0)
                                        <tr style="background-color: #F3F3F3">
                                            <th colspan="10" align="right"> Bundle Offer</th>
                                        </tr>

                                        @php
                                        $bs =1;
                                        @endphp

                                        @foreach($resultBundleOffersGift as $gifts)
                                        <tr style="background-color: #F3F3F3">
                                            <td colspan="2" align="right"> {{ $bs }} </td>
                                            <td colspan="3"> {{ $gifts->name }} </td>
                                            <td style="text-align: right;"> {{ $gifts->stockQty }} </td>
                                            <td style="text-align: right;"> {{ number_format($gifts->stockQty * $gifts->depo,2) }} </td>
                                            <td colspan="3"> </td>     
                                        </tr>
                                        @php
                                        $bs ++;
                                        @endphp
                                        @endforeach

                                        @endif

                                    </tfoot>
                                </table>

							<?php if($resultCartPro[0]->order_status == 'Closed')  { ?>	
                                
								<div class="row">
                                	
                                    <div class="col-sm-1">
                                        <input name="offer_type" type="radio" id="radio_8" class="radio-col-red" value="regular" data-toggle="modal" @if(session('offersSelected')=='regular') checked="" @endif onclick="showRegularProductM()">
                                        <label for="radio_8"> Regular</label>
                                    </div>
									
									

                                    {{-- For Special offer --}}

                                    @if(sizeof($specialOffers)>0)
                                    <div class="col-sm-1">                                       
                                        
                                            <input name="offer_type" type="radio" id="radio_7" class="radio-col-red" value="exclusive" data-toggle="modal" @if(session('offersSelected')=='exclusive') checked="" @endif onclick="showSpecialProductM()">
                                            <label for="radio_7"> Exclusive </label> 
                                    </div>
                                    @endif

									@if(sizeof($resultBundleOffers)>0)
                                        <div class="col-sm-1">
                                                <input name="offer_type" type="radio" id="radio_7{{ $resultBundleOffers->iId }}" class="radio-col-red" value="{{ $resultBundleOffers->iId }}" data-toggle="modal" @if(session('offersSelected')=='bundle') checked="" @endif onclick="showBundleProduct('{{ $resultBundleOffers->iId }}','0')">
                                                <label for="radio_7{{ $resultBundleOffers->iId }}"> Bundle</label>  
                                                                                 
                                        </div>
                                     @endif   

                                    {{-- <div class="col-sm-2">                                    
                                        <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-delete">Delete</button>
                                    </div> --}}
                                    <div class="col-sm-2">                                    
                                        <a href="{{ URL('/delete-order/'.$resultInvoice->order_id.'/'.$retailderid.'/'.$routeid . '/'. $partial_id) }}" class="btn bg-pink btn-block btn-lg waves-effect"> Delete
                                                
                                            </a> 
                                    </div>
                                </div> 
								
							<?php }  // offer display check ?>  
								
								
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
								<input type="hidden" name="partial_id" id="partial_id" value="{{ $partial_id }}">
                                <input type="hidden" name="type" id="type" value="2">

                                <input type="hidden" name="totalFreeValue" id="totalFreeValue" value="{{ $totalFreeValue }}">

                                <input type="hidden" name="totalFreeValueWiseCommissionBalance" id="totalFreeValueWiseCommissionBalance" value="{{ $totalFreeValueWiseCommission }}">
                                <input type="hidden" name="netAmount" id="netAmount" value="{{ $subTotal }}">
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
									
									<a href="{{ URL('/invoice-edit/'.$resultInvoice->order_id.'/'.$retailderid.'/'.$routeid.'/'.$partial_id) }}">
										<button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
									</a>
									
									<!--
                                        <a href="{{ URL('/order-process/'.$retailderid.'/'.$routeid) }}">
                                            <button type="button" class="btn bg-red btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                                        </a>
                                    -->
									
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