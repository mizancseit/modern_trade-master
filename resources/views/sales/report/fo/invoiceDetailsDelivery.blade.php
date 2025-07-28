@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        DELIVERY REPORT
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Delivery 
                        </small>
                    </h2>
                </div>
            </div>
        </div>
    </div>


    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">      

                <div class="body" id="printMe" >    

                    @if(sizeof($resultCartPro)>0)                    
                    <div class="body">
                        
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
                                        Contact &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->mobile }}
                                    </th>
                                    <th width="55%" align="left" valign="top" style="font-weight: normal;">
                                        Chalan No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;
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
                                        Chalan Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;
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
                                        Order Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultInvoice->order_date }}
                                       <br />
                                        Order By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->display_name }} <br />
                                        Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->cell_phone }} <br />
                                        

                                    </th>
                                </tr>
                            </thead>                            
                        </table>

                        <table class="table table-bordered" style="margin-top:-10px;">
                            <thead >
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

                            <tbody class="print-page" >

                                @if(sizeof($resultCartPro)>0)
                                @php
                                $serial   = 1;
                                $count    = 1;
                                $subTotal = 0;
                                $totalQty = 0;
                                $totalWastage = 0;
                                $totalRepDelv = 0;
                                $totalPrice = 0;
                                $totalFreeQty = 0;
                                $totalDeliveryQty = 0;
                                $totalDeliveryPrice = 0;
                                $totalPerUnitPrice = 0;

                                @endphp
                                @foreach($resultCartPro as $items)                                       
                               <tr>
                                        <th></th>
                                        <th class="rowTableLeft">{{ $items->catname }}</th>
                                        <th colspan="8"></th>
                                </tr>
                                    @php 
                                    $itemsCount = 1;
                                    $reultPro  = DB::table('tbl_order_details')
                                    ->select('tbl_order_details.p_unit_price','tbl_order_details.free_qty','tbl_order_details.delivered_qty','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                                    ->where('tbl_order.order_type','Delivered')
                                    ->where('tbl_order_details.order_id',$orderMainId)
                                    ->where('tbl_order_details.cat_id', $items->catid)    
                                    ->get();
                                    //dd($reultPro);
                                    @endphp
                                    @foreach ($reultPro as $itemsPro)
                                    @php
                                    $subTotal += $itemsPro->p_total_price;
                                    $totalQty += $itemsPro->order_qty;
                                    $totalDeliveryQty += $itemsPro->delivered_qty;
                                    $totalDeliveryPrice += $itemsPro->delivered_qty* $itemsPro->p_unit_price;
                                    $totalWastage += $itemsPro->wastage_qty;
                                    $totalRepDelv += $itemsPro->replace_delivered_qty;
                                    $totalPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;
                                    $totalFreeQty += $itemsPro->free_qty;
                                    $totalPerUnitPrice += $itemsPro->p_unit_price;

                                    @endphp

                                    <tr>
                                        <th class="rowTableCenter">{{ $serial }}</th>
                                      
                                        <th class="rowTableLeft">{{ $itemsPro->proname }}</th>
                                        <th class="rowTableRight">{{ number_format($itemsPro->order_qty,0) }}</th>
                                        <td class="rowTableRight"> {{ number_format($itemsPro->p_unit_price,2) }} </td>
                                        <th class="rowTableRight">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,2) }}</th>
                                        <th class="rowTableRight">-</th>
                                        <th class="rowTableRight">{{ number_format($itemsPro->delivered_qty,0) }}</th>
                                        <th class="rowTableRight" id="rowPrice{{ $serial }}">{{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,2) }}</th>
                                        <th class="rowTableRight">@if($itemsPro->wastage_qty=='') {{ '-' }} @else {{ number_format($itemsPro->wastage_qty,0) }} @endif</th>
                                        <th class="rowTableRight">@if($itemsPro->replace_delivered_qty=='') {{ '-' }} @else {{ number_format($itemsPro->replace_delivered_qty,0) }} @endif</th>
                                    </tr>
                                    @php
                                    $serial ++;
                                    @endphp
                                    @endforeach                                            
                                </tr>

                                @php                                             
                                $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                ->select('fq.type','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname','fq.status','fq.free_id')
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
                                $serial ++;
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
                                    <th class="rowTableLeft">{{ $itemsPro->proname }} [FREE]</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">{{ $itemsPro->total_free_qty }}</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight"> </th>     
                                    <th class="rowTableRight"> </th>       
                                    <th class="rowTableRight"> </th>
                                </tr>

                                @if(sizeof($reultRegularAnd)>0)
                                @php
                                $totalFreeQty += $reultRegularAnd->total_free_qty;
                                $serial ++;                                                    
                                @endphp 
                                <tr>
                                    <th class="rowTableCenter">{{ $serial }}</th>                                            
                                    <th class="rowTableLeft">{{ $reultRegularAnd->proname }} [FREE]</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight">{{ $reultRegularAnd->total_free_qty }}</th>
                                    <th class="rowTableRight">-</th>
                                    <th class="rowTableRight"> </th>     
                                    <th class="rowTableRight"> </th>       
                                    <th class="rowTableRight"> </th>
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
                                    <th class="rowTableLeft">{{ $itemsPro->proname }} [SP FREE]</th>
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
                                    <th class="rowTableLeft">{{ $reultSpecialAnd->proname }} [SP FREE]</th>
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

                                @endforeach
                                @endif

                                <tr>
                                    <th colspan="2"  class="rowTableLeft"> Grand Total </th>
                                    <th class="rowTableRight">{{ number_format($totalQty,0) }}</th>
                                    <th  class="rowTableRight">&nbsp;</th>
                                    <th  class="rowTableRight">{{ number_format($totalPrice,2) }}</th>
                                    <th  class="rowTableRight">{{ number_format($totalFreeQty,0) }}</th>
                                    <th class="rowTableRight" id="totalQty">{{ number_format($totalDeliveryQty,0) }}</th>
                                    <th class="rowTableRight" id="totalPrice">{{ number_format($totalDeliveryPrice,2) }}</th>
                                    <th class="rowTableRight">{{ number_format($totalWastage,0) }}</th>
                                    <th class="rowTableRight">{{ number_format($totalRepDelv,0) }}</th>                                            
                                </tr>

                                @php
                                $com = 0;
                                $reultProRate  = DB::select("SELECT * FROM tbl_ims_offer_slab WHERE group_id='".Auth::user()->business_type_id."' AND '$subTotal' BETWEEN min_value AND max_value LIMIT 1");

                                if(sizeof($reultProRate)>0)
                                {
                                    $com += $reultProRate[0]->rate;                      
                                }

                                @endphp

                                @if(sizeof($reultProRate)>0)
                                <tr>
                                    <th colspan="3"  class="rowTableLeft">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th class="rowTableRight">
                                        @if(sizeof($reultProRate)>0)                                                
                                        {{ number_format(($totalDeliveryPrice * $com)/100, 0) }}
                                        @else
                                        0
                                        @endif
                                    </th>
                                    <th>&nbsp;</th>          
                                </tr>
                                @endif

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
                                                <th colspan="6" class="rowTableRight">Commission For {{$commissionItem->catname}}</th>
                                                <th class="rowTableRight"> {{ $netAmount = number_format($commissionItem->value,0) }}</th>
                                                <th colspan="4" align="right"></th>
                                            </tr>   

                                            @php
                                            $catvaluecommission += $commissionItem->value;

                                            @endphp

                                             @endforeach  
                                             @endif
                                <tr>
                                    <th colspan="2"  class="rowTableLeft">Net Amount</th>
                                    <th class="rowTableRight">&nbsp;</th>
                                    <th class="rowTableRight">&nbsp;</th>
                                    <th class="rowTableRight">&nbsp;</th>
                                    <th class="rowTableRight">&nbsp;</th>
                                    <th class="rowTableRight">&nbsp;</th>
                                    <th class="rowTableRight" id="net_amount">
                                        @if(sizeof($reultProRate)>0)                                                
                                        {{ number_format($totalDeliveryPrice - ($totalDeliveryPrice * $com)/100, 2) }}
                                        @else
                                        {{ number_format($totalDeliveryPrice -$catvaluecommission,2) }}
                                        @endif   
                                    </th>
                                    <th class="rowTableRight">&nbsp;</th>                                            
                                    <th class="rowTableRight">&nbsp;</th>                                           
                                </tr>
                                
                                <tr>
                                    <th colspan="10" class="rowTableRight">&nbsp;</th>
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
                            <tr>
                                <th class="rowTableLeft" @if(Auth::user()->user_type_id==5) colspan="7" @else colspan="4" @endif> 
                                    @php
                                    $catQuery = DB::table('tbl_special_value_wise_category')
                                    ->select('tbl_special_value_wise_category.svwid','tbl_special_value_wise_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                    ->leftJoin('tbl_product_category','tbl_special_value_wise_category.categoryid','=','tbl_product_category.id')

                                    ->where('tbl_special_value_wise_category.svwid', $svwRows->offer_id)
                                    ->get();

                                    foreach($catQuery as $svwcRows)
                                    {
                                        echo $catNames = $svwcRows->name.' ,  ';
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

                            <tr>
                                <th class="rowTableLeft" @if(Auth::user()->user_type_id==5) colspan="10" @else colspan="6" @endif> Exclusive Value Wise Free Items </th>
                            </tr>
                            @php
                            $cPrice =0;
                            $cqty =0;
                            $fs=1;
                            @endphp

                            @foreach($commissionWiseItem as $items)
                            @php
                            $cqty +=$items->total_free_qty;
                            $cPrice +=$items->free_value;
                            @endphp

                            <tr>
                                <th class="rowTableCenter"> {{ $fs }} </th>  
                                <th colspan="1" class="rowTableLeft"> {{ $items->name }} </th>            
                                <th class="rowTableRight">{{ number_format($items->total_free_qty,0) }}</th>
                                <th class="rowTableRight">{{ number_format($items->free_value,2) }}</th>
                                <th class="rowTableRight">&nbsp; </th>
                                @if(Auth::user()->user_type_id==5)
                                <th class="rowTableRight">&nbsp;</th>
                                <th class="rowTableRight">&nbsp;</th>
                                <th class="rowTableRight">&nbsp;</th>                             
                                <th class="rowTableRight">&nbsp;</th>                             
                                <th class="rowTableRight">&nbsp;</th>                             

                                @endif                          

                            </tr>
                            @php
                            $fs ++;
                            @endphp
                            @endforeach
                            <tr>

                                <th colspan="2" class="rowTableLeft"> Total Free</th>            
                                <th class="rowTableRight">{{ number_format($cqty,0) }}</th>
                                <th class="rowTableRight">{{ number_format($cPrice,2) }}</th>
                                <th class="rowTableRight">&nbsp;</th>
                                @if(Auth::user()->user_type_id==5)
                                <th class="rowTableRight">&nbsp;</th>
                                <th class="rowTableRight">&nbsp;</th>
                                <th class="rowTableRight">&nbsp;</th>                             
                                <th class="rowTableRight">&nbsp;</th>                             
                                <th class="rowTableRight">&nbsp;</th>
                                @endif                      
                            </tr>
                            @endif
                            
                            
                            
                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="">
                                                <th colspan="11" class="rowTableLeft"> Bundle Offer</th>
                                            </tr>

                                            @php
                                            $bs =1;
                                            @endphp

                                            @foreach($resultBundleOffersGift as $gifts)
                                            <tr style="">
                                                <th colspan="1" class="rowTableCenter"> {{ $bs }} </th>
                                                <th colspan="4" class="rowTableLeft"> {{ $gifts->name }} </th>
                                                <th class="rowTableRight"> {{ $gifts->stockQty }} </th>
                                                <th class="rowTableRight"> {{ number_format($gifts->stockQty * $gifts->depo,2) }} </th>
                                                <th colspan="3" class="rowTableLeft"> </th>     
                                            </tr>
                                            @php
                                            $bs ++;
                                            @endphp
                                            @endforeach

                            @endif

                        </table>

        </div>

    </div>

    <div class="row" style="text-align: center; padding-bottom: 20px;">
        <div class="col-sm-12">
            <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                <i class="material-icons">print</i>
                <span>PRINT</span>
            </button>
        </div>
    </div>

    @endif
</div>
</div>
</div>

<!-- #END# Basic Validation -->            
</div>
</section>
@endsection