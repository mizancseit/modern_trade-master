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
                    {{-- <div class="header">
                        <h2>DELIVERY ENTRY</h2>                            
                    </div> --}}
                    <div class="body">
                        
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th width="85%" align="left" valign="top">
                                        <span style="font-size: 18px; text-transform: uppercase; color: #263F93;"> {{ $resultDistributorInfo->first_name }} </span>  
                                    </th>

                                    <th align="right" valign="top" style="border:1px solid #EEEEEE;text-align: center;margin: 20px;">
                                        <span style="font-size: 18px;"> Chalan </span>  
                                    </th>
                                </tr>
                            </thead>
                        </table>
                        <p> &nbsp; </p>

                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th width="70%" height="49" align="left" valign="top" style="font-weight: normal;">
                                        Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                        Code &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->sap_code }}<br />
                                        Route &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                        Mobile &nbsp; : {{ $resultDistributorInfo->cell_phone }}
                                        <p></p>
                                        To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                        Mobile &nbsp; : {{ $resultInvoice->mobile }}
                                    </th>
                                    <th width="30%" align="left" valign="top" style="font-weight: normal;">
                                        Chalan No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
                                        @php
                                        $cs = 0;
                                        @endphp
                                        @foreach($resultAllChalan as $dchalan)
                                        @if($cs>0) {{ $dchalan->delivery_challan.',' }} @else {{ $dchalan->delivery_challan }} @endif<br />

                                        @php
                                        $cs ++;
                                        @endphp
                                        @endforeach

                                        Chalan Date &nbsp;&nbsp;&nbsp;&nbsp; : 
                                        @php
                                        $csd = 0;
                                        @endphp
                                        @foreach($resultAllChalan as $dchalan)
                                        @if($csd>0) {{ $dchalan->delivered_date.',' }} @else {{ $dchalan->delivered_date }} @endif<br />

                                        @php
                                        $csd ++;
                                        @endphp
                                        @endforeach<br />                                        
                                        Invoice No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_no }} <br />
                                        Invoice Date &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_date }}
                                        <p></p>
                                        Collected By &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->first_name }} <br />
                                        Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }} <br />
                                        

                                    </th>
                                </tr>
                            </thead>                            
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Product Name</th>
                                    <th>Order</th>
                                    <th>P.U.P</th>                                        
                                    <th>Value</th>
                                    <th>Free</th>
                                    <th>D.Qty</th>
                                    <th>D.Value</th>
                                    <th>Wastage</th>                                            
                                    <th>R.Delivery</th>                                            
                                </tr>
                            </thead>

                            <tbody class="print-page">

                                @if(sizeof($resultCartPro)>0)
                                @php
                                $serial   = 1;
                                $count    = 1;
                                $subTotal = 0;
                                $totalQty = 0;
                                $totalWastage = 0;
                                $totalPrice = 0;
                                $totalFreeQty = 0;
                                $totalDeliveryQty = 0;
                                $totalDeliveryPrice = 0;
                                $totalPerUnitPrice = 0;

                                @endphp
                                @foreach($resultCartPro as $items)                                       
                                <tr>

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
                                    $totalPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;
                                    $totalFreeQty += $itemsPro->free_qty;
                                    $totalPerUnitPrice += $itemsPro->p_unit_price;

                                    @endphp

                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>{{ $itemsPro->proname }}</th>
                                        <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                        <td style="text-align: right;"> {{ substr($itemsPro->p_unit_price, 0, -3) }} </td>
                                        <th style="text-align: right;">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</th>
                                        <th style="text-align: right;">-</th>
                                        <th style="text-align: right;">{{ $itemsPro->delivered_qty }}</th>
                                        <th style="text-align: right;" id="rowPrice{{ $serial }}">{{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,0) }}</th>
                                        <th style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty }} @endif</th>

                                        <th style="text-align: right;">{{ $itemsPro->replace_delivered_qty }}</th>
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
                                    <th>{{ $serial }}</th>
                                    <th>{{ $itemsPro->proname }} [ FREE ]</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">{{ $itemsPro->total_free_qty }}</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;"> </th>     
                                    <th style="text-align: right;"> </th>       
                                    <th style="text-align: right;"> </th>
                                </tr>

                                @if(sizeof($reultRegularAnd)>0)
                                @php
                                $totalFreeQty += $reultRegularAnd->total_free_qty;
                                $serial ++;                                                    
                                @endphp 
                                <tr>
                                    <th>{{ $serial }}</th>                                            
                                    <th>{{ $reultRegularAnd->proname }} [ FREE ]</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;">{{ $reultRegularAnd->total_free_qty }}</th>
                                    <th style="text-align: right;">-</th>
                                    <th style="text-align: right;"> </th>     
                                    <th style="text-align: right;"> </th>       
                                    <th style="text-align: right;"> </th>
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
                                    <th>{{ $itemsPro->proname }} [ SP FREE ]</th>
                                    <th style="text-align: right;">-</th>
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
                                    <th >{{ $reultSpecialAnd->proname }} [ SP FREE ]</th>
                                    <th style="text-align: right;">-</th>
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
                                    <th colspan="2" align="right"> <b> Grand Total </b></th>
                                    <th style="text-align: right;">{{ $totalQty }}</th>
                                    <th style="text-align: right;">{{ $totalPerUnitPrice }}</th>
                                    <th style="text-align: right;">{{ number_format($totalPrice,0) }}</th>
                                    <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                    <th style="text-align: right;" id="totalQty">{{ $totalDeliveryQty }}</th>
                                    <th style="text-align: right;" id="totalPrice">{{ number_format($totalDeliveryPrice,0) }}</th>
                                    <th style="text-align: right;">{{ $totalWastage }}</th>
                                    <th>&nbsp;</th>                                            
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
                                    <th colspan="3" align="left">MEMO COMMISSION : {{ number_format($com,2) }}%</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th style="text-align: right;">
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
                                                <th colspan="6" align="right">Commission For {{$commissionItem->catname}}</th>
                                                <th style="text-align: right;"> {{ $netAmount = number_format($commissionItem->value,0) }}</th>
                                                <th colspan="4" align="right"></th>
                                            </tr>   

                                            @php
                                            $catvaluecommission += $commissionItem->value;

                                            @endphp

                                             @endforeach  
                                             @endif
                                <tr>
                                    <th colspan="2" align="right"><b> NET AMOUNT </b></th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th style="text-align: right;" id="net_amount">
                                        @if(sizeof($reultProRate)>0)                                                
                                        {{ number_format($totalDeliveryPrice - ($totalDeliveryPrice * $com)/100, 0) }}
                                        @else
                                        {{ number_format($totalDeliveryPrice -$catvaluecommission,0) }}
                                        @endif   
                                    </th>
                                    <th>&nbsp;</th>                                            
                                    <th>&nbsp;</th>                                           
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

                                <th @if(Auth::user()->user_type_id==5) colspan="3" @else colspan="4" @endif> 
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
                                <th @if(Auth::user()->user_type_id==5) colspan="7" @else colspan="2" @endif style="text-align: left; padding-left: 40px;">{{ $svwRows->commission.'%' }} 
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
                                <th colspan="1"> {{ $items->name }} </th>            
                                <th style="text-align: right;">{{ $items->total_free_qty }}</th>
                                <th style="text-align: right;">{{ $items->free_value }}</th>
                                <th>&nbsp; </th>
                                @if(Auth::user()->user_type_id==5)
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>                             
                                <th>&nbsp;</th>                             
                                <th>&nbsp;</th>                             

                                @endif                          

                            </tr>
                            @endforeach
                            <tr>

                                <th colspan="2"> Total Free</th>            
                                <th style="text-align: right;">{{ $cqty }}</th>
                                <th style="text-align: right;">{{ $cPrice.'.00' }}</th>
                                <th>&nbsp;</th>
                                @if(Auth::user()->user_type_id==5)
                                <th>&nbsp;</th>
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
                                <th colspan="2"> @if($resultBundleOffersGift->productType==2) {{ $resultBundleOffersGift->giftName }} @else {{ $resultBundleOffersGift->name }} @endif </th>
                                <th> {{ $resultBundleOffersGift->free_qty }}</th>
                                <th colspan="4"> </th>      
                            </tr>
                            @endif

                        </table>

                        @if(Auth::user()->user_type_id==5)
                        <table width="100%">
                            <thead>
                                <tr>
                                    <!-- <th width="81%" height="49" align="left" valign="top"></th> -->
                                    <th width="70%" height="49" align="left" valign="top">

                                        <?php 

/* 
$retCredLedgerData = DB::select("SELECT * FROM retailer_credit_ledger 
WHERE retailer_id = '".$resultInvoice->retailer_id."' ORDER BY 1 DESC LIMIT 1
");



if(count($retCredLedgerData)>0)
{

$retCollection = 0; 

$opening_balance = $retCredLedgerData[0]->retailer_balance;

if(sizeof($reultProRate)>0):   
$netAmount = $totalDeliveryPrice - ($totalDeliveryPrice * $com)/100;
else:
$netAmount = $totalDeliveryPrice;
endif; 

$opening_balance = $opening_balance  - $netAmount;


$newBalance =  ($opening_balance  + $netAmount);   
} */

$retCredLedgerData = DB::select("SELECT crd.* FROM retailer_credit_ledger crd 
    JOIN tbl_order tord ON tord.order_no = crd.retailer_invoice_no
    WHERE tord.retailer_id = '".$resultInvoice->retailer_id."' 
    AND tord.order_id = '".$orderMainId."' order by 1 desc limit 1
    ");

if(count($retCredLedgerData)>0)
{

    $retCollection = 0; 

    $opening_balance = $retCredLedgerData[0]->retailer_opening_balance;

    $netAmount = $retCredLedgerData[0]->retailer_invoice_sales;

    $newBalance = $retCredLedgerData[0]->retailer_balance;

//$newBalance =  ($opening_balance  + $netAmount);   
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
<tbody >
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
        <td colspan="4" height="40">&nbsp;</td>
    </tr>

    <tr>
        <td> <div style="border: .5px solid #000; width: 200px;" > </div>
            <div style="font-size:16px;margin-top:5px;margin-left:56px">Prepared By</div> </td>


            <td align="right"> <div style="border: .5px solid #000; width: 200px;" > </div>
                <div style="font-size:16px;margin-top:5px;margin-right:65px">Signed By</div> </td>
            </tr>

        </table>        

        </div>

    </div>
	
	 @endif

	
	<form action="{{ URL('/rollback-delivery') }}" method="get">
               
                {{ csrf_field() }}    <!-- token --> 
    
		<div class="row" style="text-align: center; padding-bottom: 20px;">
			<div class="col-sm-12">
				<!-- <button type="button" class="btn bg-red waves-effect" onclick="RollBackDelivery({{$resultInvoice->order_id}})"> -->
				<button type="submit" class="btn bg-red waves-effect" onclick="">
					<i class="material-icons">cloud_upload</i>
					<span>CONFIRM ORDER BACK</span>
				</button>
			</div>
			
			
		</div>
		
		<input type="hidden" name="orderid" value="{{$resultInvoice->order_id}}" />
		
	</form>	
 	

   
</div>
</div>
</div>

<!-- #END# Basic Validation -->            
</div>
</section>
@endsection