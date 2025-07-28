    @extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Invoice
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Visit </a> / Invoice
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="font-weight:">
                         <div class="body" id="printMe" >
                            <table width="100%">
                                    <thead>
                                        <tr>
                                            <th width="70%" height="49" align="left" valign="top">
                                            {{ $resultDistributorInfo->display_name }} <br />
                                            Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Route &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                            Mobile &nbsp; : {{ $resultDistributorInfo->cell_phone }}
                                            <p></p>
                                            To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                            Mobile &nbsp; : {{ $resultInvoice->mobile }}

                                            <br />
                                            Owner &nbsp; : {{ $resultInvoice->owner }}
                                            <br />
                                            Address : {{ $resultInvoice->vAddress }}
                                            
                                            {{-- <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo"> --}}
                                            </th>
                                            <th width="30%" align="left" valign="top">
                                            @if($resultInvoice->order_type=='Delivered')  
                                            INVOICE <br />
                                            Delivery Date : {{ $resultInvoice->update_date }}
                                            @else 
                                            MEMO 
                                            @endif  
                                            <br />

                                            @if($resultInvoice->order_type=='Delivered')
                                            Order No
                                            @else 
                                            Memo No 
                                            @endif
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->order_no }}

                                            <p></p>
                                            Collected By : {{ $resultFoInfo->first_name }} <br />
                                            Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }} <br />
                                            @if($resultInvoice->order_type=='Delivered')
                                            Order Date
                                            @else 
                                            Memo Date 
                                            @endif
                                             &nbsp;&nbsp;&nbsp;: {{ $resultInvoice->order_date }}

                                            </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        
                                        
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered">
                                  <thead  class="print-page">
                                    <tr>
                                      <th>SL</th>
                                      <th>Product Group</th>
                                      <th>Product Name</th>
                                      <th>Order</th>
                                      <th>Value</th>
                                      <th>Free</th>
                                      @if(Auth::user()->user_type_id==5)
                                      <th>Delivery</th>
                                      <th>Delivery Value</th>
                                      <th>Wastage</th>                                            
                                      <th>Replace Delivery</th>
                                      @endif
                                    </tr>
                                  </thead>

                                  <tbody  class="print-page">
                                            
                                            @if(sizeof($resultCartPro)>0)
                                            @php
                                            $serial   = 1;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalQty = 0;
                                            $totalWastage = 0;
                                            $totalPrice = 0;
                                            $totalFreeQty = 0;
                                            $totalReplaceQty = 0;
                                            $totalDeliveryQty = 0;
                                            $totalDeliveryValue = 0;

                                            @endphp
                                            @foreach($resultCartPro as $items)                                       
                                            <tr>
                                                <th></th>
                                                <th colspan="9">{{ $items->catname }}</th>
                                            </tr>
                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.partial_order_id','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.free_qty','tbl_order_details.p_unit_price','tbl_order_details.delivered_qty','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                    //->where('tbl_order.order_type','Confirmed')                        
                                                    ->where('tbl_order.fo_id',$foMainId)
                                                    ->where('tbl_order_details.order_id',$orderMainId)
                                                    ->where('tbl_order_details.cat_id',$items->cat_id)
                                                    ->where('tbl_order_details.partial_order_id', $items->partial_order_id)
                                                    //->groupBy('tbl_product.id')    
                                                    ->get();
                                                   //dd($reultPro);
                                                @endphp
                                                @foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalQty += $itemsPro->order_qty;
                                                $totalWastage += $itemsPro->wastage_qty;
                                                $totalFreeQty += $itemsPro->free_qty;
                                                $totalReplaceQty += $itemsPro->replace_delivered_qty;
                                                $totalPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;

                                                $totalDeliveryQty += $itemsPro->delivered_qty;
                                                $totalDeliveryValue += $itemsPro->delivered_qty * $itemsPro->p_unit_price;

                                                @endphp

                                                <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th></th>
                                                    <th>{{ $itemsPro->proname }}</th>
                                                    <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                                    <th style="text-align: right;">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</th>
                                                    <th style="text-align: right;">@if($itemsPro->free_qty!=null) {{ $itemsPro->free_qty }} @else - @endif</th>

                                                    @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">@if($itemsPro->delivered_qty!=null) {{ $itemsPro->delivered_qty }} @else - @endif</th>
                                                    <th style="text-align: right;" id="rowPrice{{ $serial }}">@if($itemsPro->delivered_qty!=null) {{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,0) }} @else - @endif
                                                    </th>                                                
                                                    <th style="text-align: right;">@if($itemsPro->wastage_qty==null) - @else {{ $itemsPro->wastage_qty }} @endif</th>

                                                    <th style="text-align: right;">@if($itemsPro->replace_delivered_qty==null) - @else {{ $itemsPro->replace_delivered_qty }} @endif</th>
                                                    @endif
                                                </tr>

                                                @php
                                                $serial ++;
                                                @endphp
                                                @endforeach

                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                    ->select('fq.type','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','fq.status','tbl_product.id','tbl_product.name AS proname','fq.free_id')
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
                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th style="text-align: right;">-</th>
                                                        <th style="text-align: right;"> </th>     
                                                        <th style="text-align: right;"> </th>       
                                                        <th style="text-align: right;"> </th>
                                                        @endif
                                                        </tr>
                                                    @endif

                                                @endforeach

                                                {{-- SPECIAL OFFER --}}    
                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_special_free_qty AS fq')
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.status','0')      
                                                    ->where('fq.order_id',$resultInvoice->order_id)
                                                    ->where('fq.catid', $items->catid)
                                                    ->get();
                                                @endphp
                                                @foreach ($reultRegularGift as $itemsPro)
                                                @php
                                                $totalFreeQty += $itemsPro->total_free_qty;

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
                                                    
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th style="text-align: right;">-</th>
                                                    <th style="text-align: right;">-</th>     
                                                    <th style="text-align: right;">-</th>       
                                                    <th style="text-align: right;">-</th>
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
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th style="text-align: right;">-</th>
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
                                           
                                            
                                            @endforeach
                                            @endif

                                            <tr>
                                                <th colspan="3" align="right">Grand Total</th>
                                                <th style="text-align: right;">{{ $totalQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalPrice,0) }}</th>
                                                <th style="text-align: right;">{{ $totalFreeQty }}</th>

                                                @if(Auth::user()->user_type_id==5)
                                                <th style="text-align: right;" id="totalQty">{{ $totalDeliveryQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <th style="text-align: right;" id="totalPrice">{{ number_format($totalDeliveryValue,0) }}</th>
                                                <th style="text-align: right;">{{ $totalWastage }}</th>
                                                <th style="text-align: right;">{{ $totalReplaceQty }}</th>
                                                @endif                                          
                                            </tr>
                                            
                                            @php
                                            $com = 0;                                            
                                            @endphp

                                            @if($resultInvoice->total_discount_percentage!=Null)
                                            <tr>
                                                <th colspan="3" align="left">MEMO COMMISSION : {{ $com=$resultInvoice->total_discount_rate }}%</th>                                                
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;">
                                                {{ $resultInvoice->total_discount_percentage }}
                                                </th>
                                                <th>&nbsp;</th>
                                                @if(Auth::user()->user_type_id==5)
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;">&nbsp;</th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>
                                                @endif                                            
                                            </tr>
                                            @endif

                                        </tbody>
                                        <tfoot  class="print-page">
                                            <tr>
                                                <th colspan="3" align="right">NET AMOUNT</th>
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;" id="net_amount">
                                                @if($resultInvoice->total_discount_percentage!=Null)                    
                                                    {{ $netAmount = number_format($totalPrice - ($totalPrice * $com)/100, 0) }}
                                                @else
                                                    {{ $netAmount = number_format($totalPrice,0) }}
                                                @endif   
                                                </th>
                                                <th>&nbsp;</th>
                                                @if(Auth::user()->user_type_id==5)
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>
                                                @endif                                           
                                            </tr>

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

                                                    $commissionAmount = $svwRows->total_free_value-$valueSum;
                                                @endphp
                                            
                                                <th @if(Auth::user()->user_type_id==5) colspan="4" @else colspan="4" @endif> 
                                                    @php
                                                    $catQuery = DB::table('tbl_special_value_wise_category')
                                                    ->select('tbl_special_value_wise_category.svwid','tbl_special_value_wise_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                    ->leftJoin('tbl_product_category','tbl_special_value_wise_category.categoryid','=','tbl_product_category.id')

                                                    ->where('tbl_special_value_wise_category.svwid', $svwRows->offer_id)
                                                    ->get();

                                                    foreach($catQuery as $svwcRows)
                                                    {
                                                        echo $catNames = $svwcRows->name.' , ';
                                                        //echo rtrim($catNames,',');
                                                    }
                                                    @endphp
                                                </th>
                                                <th @if(Auth::user()->user_type_id==5) colspan="6" @else colspan="2" @endif style="text-align: left; padding-left: 40px;">{{ $svwRows->commission.'%' }} 
                                                    {{-- <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$commissionAmount.'/'.$items->catid.'/'.$svwRows->offer_id) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD" class="btn bg-green btn-block btn-sm waves-effect" style="width: 70px;">
                                                    </a> --}}
                                                </th>
                                                @php
                                                }
                                                @endphp
                                            @endif

                                            @if(sizeof($commissionWiseItem)>0)

                                                <tr style="background:#EEEEEE;">
                                                    <th @if(Auth::user()->user_type_id==5) colspan="10" @else colspan="6" @endif> Exclusive Value Wise Free Items </th>
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
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>                             
                                                    <th>&nbsp;</th>
                                                    @endif                          
                                                                            
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    
                                                    <th colspan="3"> Total Free</th>            
                                                    <th style="text-align: right;">{{ $cqty }}</th>
                                                    <th style="text-align: right;">{{ $cPrice.'.00' }}</th>
                                                    <th>&nbsp;</th>
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>                             
                                                    <th>&nbsp;</th>
                                                    @endif                      
                                                </tr>
                                            @endif
                                            
                                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="background-color: #EEEEEE">
                                                <th colspan="2" align="right"> {{$resultBundleOffersGift->vOfferName}}</th>
                                                <th @if(Auth::user()->user_type_id==5) colspan="3" @else colspan="3" @endif> @if($resultBundleOffersGift->productType==2) {{ $resultBundleOffersGift->giftName }} @else {{ $resultBundleOffersGift->name }} @endif </th>
                                                <td align="right"> {{ $resultBundleOffersGift->free_qty }}</td> 
                                                @if(Auth::user()->user_type_id==5)
                                                <td align="right" colspan="4"></td> 
                                                @endif
                                            </tr>
                                            @endif
                                        </tfoot>                                  
                           </table>
                           
                           
                        @if(Auth::user()->user_type_id==5)   
                            <table width="100%">
                                    <thead>
                                        <tr>
                                            <th width="70%" height="49" align="left" valign="top">
                                          
                                           <?php 
                                          
                                          
                                        $retCredLedgerData = DB::select("SELECT * FROM retailer_credit_ledger 
                                                            WHERE retailer_id = '".$resultInvoice->retailer_id."' ORDER BY 1 DESC LIMIT 1
                                                        ");
                                          

                                          
                                         if(count($retCredLedgerData)>0)
                                         {
                                            $retCollection = 0;
                                            
                                            $opening_balance = $retCredLedgerData[0]->retailer_balance;;
                                            
                                            if(sizeof($reultProRate)>0):   
                                                $netAmount = $totalDeliveryValue - ($totalDeliveryValue * $com)/100;
                                            else:
                                                $netAmount = $totalDeliveryValue;
                                            endif;  
                                            
                                            $newBalance =  ($opening_balance  + $netAmount) - $retCollection;
                                           
                                         } else {
                                        
                                            $retailerData = DB::select("SELECT opening_balance FROM tbl_retailer 
                                             WHERE retailer_id = '".$resultInvoice->retailer_id."'");
                                            
                                            $opening_balance = $retailerData[0]->opening_balance;
                                            
                                            
                                            $retailerCollectData = DB::select("SELECT SUM(collection_amount) collection_amount 
                                            FROM depot_collection WHERE retailer_id = '".$resultInvoice->retailer_id."'");
                                            
                                            if(sizeof($retailerCollectData)>0):   
                                                $retCollection = $retailerCollectData[0]->collection_amount;
                                            else:
                                                $retCollection = 0;
                                            endif; 
                                            
                                            
                                            if(sizeof($reultProRate)>0):   
                                                $netAmount = $totalDeliveryValue - ($totalDeliveryValue * $com)/100;
                                            else:
                                                $netAmount = $totalDeliveryValue;
                                            endif;  
                                        
                                            
                                            $newBalance =  ($opening_balance  + $netAmount) - $retCollection;
                                            
                                         }
                                    

                                        
                                            
                                        
                                    ?>
                                          
                                            Opening Balance &nbsp;: {{ number_format($opening_balance,0) }}<br />
                                            Today Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ number_format($netAmount,0) }}<br />
                                            Balance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ number_format($newBalance,0) }}<br /> 
                                           
                                            
                                        
                                            </th>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                    </tbody>
                                </table>
                        @endif

                        <br/>
                             <table width="100%">                             
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>                                     
                                    <tr>
                                           <td> <div style="border: 1px solid; background-color:purple; width: 300px;" > </div>
                                           <div style="font-size:16px;font-weight:bold;margin-top:5px;margin-left:100px">Prepared By</div> </td>
                                           
                                           
                                           <td align="right"> <div style="border: 1px solid; background-color:purple; width: 300px;" > </div>
                                           <div style="font-size:16px;font-weight:bold;margin-top:5px;margin-right:110px">Signed By</div> </td>
                                    </tr>
                                       
                             </table>       
                           
                        </div>

                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>
                        <p>&nbps;</p>

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection