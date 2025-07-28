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
                                    <th width="77%" align="left" valign="top">
                                        <span style="font-family: arial;font-size:12;font-weight: bold; text-transform: uppercase; color: #263F93;">
                                            {{ Auth::user()->display_name}} <!-- {{ $resultDistributorInfo->first_name }} --> </span>  
                                    </th>

                                    <th align="right" valign="top" style="text-align: center;margin: 15px;">
                                    <?php if($resultDistributorInfo->business_type_id == 1 ) { ?>
                                        <span style="font-family: arial;font-size:12;font-weight: bold;"> Delivery Memo (Lighting) </span> 
                                    <?php } elseif($resultDistributorInfo->business_type_id == 2) { ?>
                                        <span style="font-family: arial;font-size:12;font-weight: bold;"> Delivery Memo (Accessories) </span>  
                                    <?php } ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                     

                        <table class="table table-bordered" width="100%" style="margin-top:5px;font-family: arial;font-size:8;font-weight: normal;">
                            <thead>
                                <tr>
                                    <th width="45%" align="left" valign="top" style="font-weight: normal;vertical-align:top">
                                        Point &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultDistributorInfo->point_name }}<br />
                                        Code&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultDistributorInfo->sap_code }}<br />
                                        Contact&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultDistributorInfo->cell_phone }}<br />
                                        Route &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultDistributorInfo->rname }}
                                       <br/>
                                        Retailer &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}<br />
                                        Address &nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->vAddress }}<br />
										Contact &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->mobile }}
                                    </th>
                                    <th width="55%" align="left" valign="top" style="font-weight: normal;vertical-align:top">
                                        Chalan No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;
                                        @php
                                        $cs = 0;
                                        @endphp
                                        @foreach($resultAllChalan as $dchalan)
                                        @if($cs>0) 

                                        {{ $deliChallan = $dchalan->delivery_challan.','  }} @else {{ $dchalan->delivery_challan }} @endif

                                        @php
                                        $cs ++;
                                        @endphp
                                        @endforeach
                                        <br />
                                        Chalan Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;
                                        @php
                                        $csd = 0;
                                        @endphp
                                        @foreach($resultAllChalan as $dchalan)
                                        @if($csd>0) {{ $dchalan->delivered_date.',' }} @else {{ $dchalan->delivered_date }} @endif

                                        @php
                                        $csd ++;
                                        @endphp
                                        @endforeach<br />                                        
                                        Order No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultInvoice->order_no }} <br />
                                        Order Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultAllChalan[0]->ordered_date }}
                                       <br />
                                        Order By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->display_name }} <br />
                                        Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->cell_phone }} <br />
                                        

                                    </th>
                                </tr>
                            </thead>                            
                        </table>
                                <table class="table table-bordered">
                                  <thead  class="print-page">
                                    <tr>
                                    <th class="headerTable" width="3%" valign="middle">Sl.</th>
                                    <th class="headerTable" width="37%" valign="middle">Item</th>
                                    <th class="headerTable" width="7%" valign="middle">Order Qty</th>
                                    <th class="headerTable" width="7%" valign="middle">Unit Price</th>                                        
                                    <th class="headerTable" width="8%" valign="middle">Order Value</th>
                                    <th class="headerTable" width="7%" valign="middle">Free Qty</th>
                                    <th class="headerTable" width="8%" valign="middle">Delivery Qty</th>
                                    <th class="headerTable" width="9%" valign="middle">Delivery Value</th>
                                    <th class="headerTable" width="7%" valign="middle">Wastage</th>
                                    <th class="headerTable" width="7%" valign="middle">Replace</th>                                            
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
                                                <th class="rowTableLeft" colspan="9">{{ $items->catname }}</th>
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
                                                    <th class="rowTableCenter">{{ $serial }}</th>
                                                 
                                                    <th class="rowTableLeft">{{ $itemsPro->proname }}</th>
                                                    <th class="rowTableRight">{{ $itemsPro->order_qty }}</th>
                                                    <td class="rowTableRight"> {{ number_format($itemsPro->p_unit_price,2) }} </td>
                                                    <th class="rowTableRight">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</th>
                                                    <th class="rowTableRight">@if($itemsPro->free_qty!=null) {{ $itemsPro->free_qty }} @else - @endif</th>

                                                    @if(Auth::user()->user_type_id==5)
                                                    <th class="rowTableRight">@if($itemsPro->delivered_qty!=null) {{ $itemsPro->delivered_qty }} @else - @endif</th>
                                                    <th class="rowTableRight" id="rowPrice{{ $serial }}">@if($itemsPro->delivered_qty!=null) {{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,0) }} @else - @endif
                                                    </th>                                                
                                                    <th class="rowTableRight">@if($itemsPro->wastage_qty==null) - @else {{ $itemsPro->wastage_qty }} @endif</th>

                                                    <th class="rowTableRight">@if($itemsPro->replace_delivered_qty==null) - @else {{ $itemsPro->replace_delivered_qty }} @endif</th>
                                                    @endif
                                                </tr>

                                                @php
                                                $serial ++;
                                                @endphp
                                                @endforeach
                                                @if($items->partial_order_id == 'part_'.$items->order_count && $items->order_det_status=='Closed')
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
                                                    <th class="rowTableCenter">{{ $serial }}</th>
                                                    <th class="rowTableLeft">{{ $itemsPro->proname }} [ FREE ]</th>
                                                    <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight">-</th>
                                                     <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight">{{ $itemsPro->total_free_qty }}</th>

                                                    @if(Auth::user()->user_type_id==5)
                                                    <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight"> </th>     
                                                    <th class="rowTableRight"> </th>       
                                                    <th class="rowTableRight"> </th>
                                                    @endif
                                                    </tr>

                                                    @if(sizeof($reultRegularAnd)>0)
                                                    @php
                                                    $totalFreeQty += $reultRegularAnd->total_free_qty;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <th class="rowTableCenter">{{ $serial }}</th>
                                                        <th class="rowTableLeft">{{ $reultRegularAnd->proname }} [ FREE ]</th>
                                                        <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">-</th>
                                                         <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">{{ $reultRegularAnd->total_free_qty }}</th>
                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight"> </th>     
                                                        <th class="rowTableRight"> </th>       
                                                        <th class="rowTableRight"> </th>
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
                                                    <th class="rowTableCenter">{{ $serial }}</th>
                                                    <th class="rowTableLeft">{{ $itemsPro->proname }} [ SP FREE ]</th>
                                                     <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight">-</th>
                                                     <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight">{{ $itemsPro->total_free_qty }}</th>
                                                    
                                                    @if(Auth::user()->user_type_id==5)
                                                    <th class="rowTableRight">-</th>
                                                    <th class="rowTableRight">-</th>
                                                     <th class="rowTableRight">-</th>      
                                                    <th class="rowTableRight">-</th>
                                                    @endif
                                                    </tr>

                                                    @if(sizeof($reultSpecialAnd)>0)
                                                    @php
                                                    $totalFreeQty += $reultSpecialAnd->total_free_qty;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <th class="rowTableCenter">{{ $serial }}</th>
                                                       
                                                        <th class="rowTableLeft">{{ $reultSpecialAnd->proname }} [ SP FREE ]</th>
                                                        <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">-</th>
                                                         <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">{{ $reultSpecialAnd->total_free_qty }}</th>                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">-</th>
                                                        <th class="rowTableRight">-</th>       
                                                        <th class="rowTableRight">-</th>
                                                        @endif
                                                        </tr>
                                                    @endif

                                                @php
                                                $serial ++;
                                                @endphp                       
                                                @endforeach                                            
                                           @endif
                                            
                                            @endforeach
                                            @endif

                                             <tr>
                                                <th colspan="2"  class="rowTableLeft"> Grand Total </th>
                                                <th class="rowTableRight">{{ number_format($totalQty,0) }}</th>
                                                <th  class="rowTableRight">&nbsp;</th>
                                                <th  class="rowTableRight">{{ number_format($totalPrice,2) }}</th>
                                                <th  class="rowTableRight">{{ number_format($totalFreeQty,0) }}</th>
                                                <th class="rowTableRight" id="totalQty">{{ number_format($totalDeliveryQty,0) }}</th>
                                                <th class="rowTableRight" id="totalPrice">{{ number_format($totalDeliveryValue,2) }}</th>
                                                <th class="rowTableRight">{{ number_format($totalWastage,0) }}</th>
                                                <th class="rowTableRight">{{ number_format($totalReplaceQty,0) }}</th>                                            
                                            </tr>
                                            @php
                                            $com = 0;                                            
                                            @endphp

                                            @if($resultInvoice->total_discount_percentage!=Null)
                                            <tr>
                                                <th colspan="3"  class="rowTableLeft">MEMO COMMISSION : {{ $com=$resultInvoice->total_discount_rate }}%</th>                                                
                                                <th>&nbsp;</th>
                                                <th class="rowTableRight">
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
                                                <th colspan="2"  class="rowTableLeft">Net Amount</th>
                                                <th></th>
                                                 <th></th>
                                                <th class="rowTableRight" id="net_amount">
                                                @if($resultInvoice->total_discount_percentage!=Null)                    
                                                    {{ $netAmount = number_format($totalPrice - ($totalPrice * $com)/100, 2) }}
                                                @else
                                                    {{ $netAmount = number_format($totalPrice,2) }}
                                                @endif   
                                                </th>
                                              <th class="rowTableRight">&nbsp;</th>
                                                @if(Auth::user()->user_type_id==5)
                                                <th class="rowTableRight">&nbsp;</th>
                                               <th class="rowTableRight">&nbsp;</th>
                                                <th class="rowTableRight">&nbsp;</th>                                   
                                                <th class="rowTableRight">&nbsp;</th>
                                                @endif                                           
                                            </tr>
                                         @if($items->partial_order_id == 'part_'.$items->order_count && $items->order_det_status=='Closed')
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
                                            <tr>
                                                <th  class="rowTableLeft" @if(Auth::user()->user_type_id==5) colspan="7" @else colspan="4" @endif> 
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
                                                <th colspan="1" class="rowTableRight">{{ $svwRows->commission.'%' }}
                                                 </th>
                                                 <th colspan="2" class="rowTableRight">{{number_format(($svwRows->total_free_value* $svwRows->commission)/100,2)}}</th>
                                            </tr>
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

                                        @endif
                                        </tfoot>                                  
                           </table>
                           
                           
                        @if(Auth::user()->user_type_id==5)   
                    <table width="100%">
                                    <thead>
                                        

                                        <?php 

                                $retCredLedgerData = DB::select("SELECT crd.* FROM retailer_credit_ledger crd 
                                    JOIN tbl_order tord ON tord.order_no = crd.retailer_invoice_no
                                    WHERE tord.retailer_id = '".$resultInvoice->retailer_id."' 
                                    AND tord.order_id = '".$orderMainId."' AND crd.partial_order_id = '".$orderPartial."' order by 1 desc limit 1
                                    ");
                                $opening_balance = 0;
                                $netAmount = 0;
                                $newBalance = 0;

                                if(count($retCredLedgerData)>0)
                                {

                                    $retCollection = 0; 

                                    $opening_balance = $retCredLedgerData[0]->retailer_opening_balance;

                                    $netAmount = $retCredLedgerData[0]->retailer_invoice_sales;

                                    $newBalance = $retCredLedgerData[0]->retailer_balance;

                                //$newBalance =  ($opening_balance  + $netAmount);   
                                } 

                                ?>

                        <tr>
                            <th width="70%" height="49" align="left" style="text-align: left; font-family: arial;font-size:8;font-weight: normal;" valign="top">

                            Opening Balance &nbsp;: {{ number_format($opening_balance,2) }}<br />
                            Today Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ number_format($netAmount,2) }}<br />
                            Closing Balance &nbsp;&nbsp;&nbsp;: {{ number_format($newBalance,2) }}<br /> 



                            </th>
                                           
                        </tr> 
                         

                        {{-- <tr>
                            <th width="150" align="left">Opening Balance &nbsp;: </th> 
                            <th width="50" align="right">{{ number_format($opening_balance,2) }} </th>
                        </tr>
                        <tr>
                            <th width="150" align="left">Today Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </th>
                            <th width="50" align="right">{{ number_format($netAmount,2) }}</th>
                        </tr>
                        <tr>
                            <th width="150" align="left"> Closing Balance &nbsp;&nbsp;&nbsp;:</th>
                            <th width="50" align="right"> {{ number_format($newBalance,2) }}</th>

                        </tr> --}}
                    </thead>
                   
                </table>
                        @endif

                        <br/>
        <table width="100%">
            <tr>
                <td colspan="3">&nbsp;</td>

           </tr>

            <tr>
                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                    <div style="text-align: center; font-face:arial;font-size:8;font-weight: normal;margin-top:5px;margin-left:0px">Prepared By</div>
                </td>

				<!--
                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                    <div style="text-align: center; font-face:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:0px">Name & Sig. of FO/SFO</div> 
                </td> -->

                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                    <div style="text-align: center; font-face:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:0px">Received By</div> 
                </td>
            </tr>

        </table>      
                           
                        </div>

                        <div class="row" style="text-align: center; padding-bottom: 20px;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection