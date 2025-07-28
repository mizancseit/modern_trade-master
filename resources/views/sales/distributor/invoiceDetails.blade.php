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
                                        <th width="85%" align="left" valign="top">
                                            <span style="font-size: 18px; text-transform: uppercase; color: #263F93;"> {{ $resultDistributorInfo->display_name }} </span>  
                                        </th>

                                        <th align="right" valign="top" style="border:1px solid #EEEEEE;text-align: center;margin: 20px;">
                                            <span style="font-size: 18px;"> Invoice </span>  
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <p> &nbsp; </p>

                            <table class="table table-bordered">
                                    <tdead>
                                        <tr>
                                            <td width="40%" height="49" align="left" valign="top">
                                            <!-- {{ $resultDistributorInfo->display_name }} <br /> -->
                                            Point &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultDistributorInfo->point_name }}<br />
                                            Code &nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultDistributorInfo->sap_code }} <br />
											Route &nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultDistributorInfo->rname }}<br />
                                            Mobile &nbsp; : {{ $resultDistributorInfo->cell_phone }}
                                            <p></p>
                                            To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b> {{ $resultInvoice->name }} </b> <br />
                                            Mobile &nbsp; : {{ $resultInvoice->mobile }}
                                            <br />
                                            Owner &nbsp;&nbsp; : {{ $resultInvoice->owner }}
                                            <br />
                                            Address : {{ $resultInvoice->vAddress }}
                                            
                                            @if($resultInvoice->order_type!='Delivered') 
                                            <span id="watermark">
                                                <p id="bg-text"> NOT CONFIRM </p> 
                                            </span>
                                            @endif
                                            </td>
                                            
                                            <td width="30%" align="left" valign="top">

                                            @if($resultInvoice->order_type=='Delivered')
                                            Chalan No : {{ $resultCartPro[0]->delivery_challan }} <br />
                                            Chalan Date : {{ $resultCartPro[0]->delivered_date }} <br />

                                            @else
                                            Invoice No &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_no}} <br />
                                            Invoice Date &nbsp; : {{ $resultInvoice->order_date }} <br />

                                            @endif

                                            Collected By &nbsp;&nbsp;: {{ $resultFoInfo->first_name }} <br />
                                            Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }} <br />

                                            &nbsp;&nbsp;&nbsp;
                                             @if($resultInvoice->print_count==0)
                                             <span id="original" style="font-size: 18px;"> <br /> Original Copy</span>
                                             @else
                                             <span id="duplicate" style="font-size: 18px;"><br /> Duplicate Copy</span>
                                             @endif

                                             <span id="duplicate1" style="font-size: 18px; display: none;"><br /> Duplicate Copy</span>

                                             <input type="hidden" name="invoicePrint" id="invoicePrint" value="{{ $resultInvoice->print_count }}">

                                            </td>
                                        </tr>
                                    </thead>
                                    
                                </table>
                                <table class="table table-bordered">
                                  <tdead>
                                    <tr>
                                      <td>SL</td>
                                      <td>Name</td>
                                      <td>Order</td>
                                      <td>P.U.P</td>
                                      <td>Value</td>
                                      <td>Free</td>
                                      @if(Auth::user()->user_type_id==5)
                                      <td>D.Qty</td>
                                      <td>D.Value</td>
                                      <td>Wastage</td>                                            
                                      <td>R.Delivery</td>
                                      @endif
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
                                            $totalReplaceQty = 0;
                                            $totalDeliveryQty = 0;
                                            $totalDeliveryValue = 0;
                                            $totalPerUnitPrice = 0;

                                            @endphp
                                            @foreach($resultCartPro as $items)                                       
                                            <tr>
                                               
                                                <td colspan="10">{{ $items->catname }}</td>
                                            </tr>
                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.p_unit_price','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.free_qty','tbl_order_details.p_unit_price','tbl_order_details.delivered_qty','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                    //->where('tbl_order.order_type','Confirmed')                        
                                                    ->where('tbl_order.fo_id',$foMainId)                        
                                                    ->where('tbl_order_details.order_id',$orderMainId)
                                                    ->where('tbl_order_details.cat_id', $items->catid)    
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
                                                $totalPerUnitPrice += $itemsPro->p_unit_price;
                                                @endphp

                                                <tr>
                                                    <td>{{ $serial }}</td>
                                                    <td>{{ $itemsPro->proname }}</td>
                                                    <td style="text-align: right;">{{ $itemsPro->order_qty }}</td>
                                                    <td style="text-align: right;"> {{ substr($itemsPro->p_unit_price, 0, -3) }} </td>
                                                    <td style="text-align: right;">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</td>
                                                    <td style="text-align: right;">@if($itemsPro->free_qty!=null) {{ $itemsPro->free_qty }} @else - @endif</td>

                                                    @if(Auth::user()->user_type_id==5)
                                                    <td style="text-align: right;">@if($itemsPro->delivered_qty!=null) {{ $itemsPro->delivered_qty }} @else - @endif</td>
                                                    <td style="text-align: right;" id="rowPrice{{ $serial }}">@if($itemsPro->delivered_qty!=null) {{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,0) }} @else - @endif
                                                    </td>                                                
                                                    <td style="text-align: right;">@if($itemsPro->wastage_qty==null) - @else {{ $itemsPro->wastage_qty }} @endif</td>

                                                    <td style="text-align: right;">@if($itemsPro->replace_delivered_qty==null) - @else {{ $itemsPro->replace_delivered_qty }} @endif</td>
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
                                                    <td>{{ $serial }}</td>
                                                    <td>{{ $itemsPro->proname }} [ FREE ]</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">{{ $itemsPro->total_free_qty }}</td>

                                                    @if(Auth::user()->user_type_id==5)
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;"> </td>     
                                                    <td style="text-align: right;"> </td>       
                                                    <td style="text-align: right;"> </td>
                                                    @endif
                                                    </tr>

                                                    @if(sizeof($reultRegularAnd)>0)
                                                    @php
                                                    $totalFreeQty += $reultRegularAnd->total_free_qty;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <td>{{ $serial }}</td>
                                                        <td>{{ $reultRegularAnd->proname }} [ FREE ]</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">{{ $reultRegularAnd->total_free_qty }}</td>
                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;"> </td>     
                                                        <td style="text-align: right;"> </td>       
                                                        <td style="text-align: right;"> </td>
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
                                                    <td>{{ $serial }}</td>
                                                    <td>{{ $itemsPro->proname }} [ SP FREE ]</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">{{ $itemsPro->total_free_qty }}</td>
                                                    
                                                    @if(Auth::user()->user_type_id==5)
                                                    <td style="text-align: right;">-</td>
                                                    <td style="text-align: right;">-</td>     
                                                    <td style="text-align: right;">-</td>       
                                                    <td style="text-align: right;">-</td>
                                                    @endif
                                                    </tr>

                                                    @if(sizeof($reultSpecialAnd)>0)
                                                    @php
                                                    $totalFreeQty += $reultSpecialAnd->total_free_qty;
                                                    $serial ++;                                                    
                                                    @endphp 
                                                        <tr>
                                                        <td>{{ $serial }}</td>
                                                        <td>{{ $reultSpecialAnd->proname }} [ SP FREE ]</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">{{ $reultSpecialAnd->total_free_qty }}</td>                                                        
                                                        @if(Auth::user()->user_type_id==5)
                                                        <td style="text-align: right;">-</td>
                                                        <td style="text-align: right;">-</td>     
                                                        <td style="text-align: right;">-</td>       
                                                        <td style="text-align: right;">-</td>
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
                                                <td colspan="2" align="right">Grand Total</td>
                                                <td style="text-align: right;">{{ $totalQty }}</td>
                                                <td style="text-align: right;"> {{ $totalPerUnitPrice }}</td>
                                                <td style="text-align: right;">{{ number_format($totalPrice,0) }}</td>
                                                <td style="text-align: right;">{{ $totalFreeQty }}</td>

                                                @if(Auth::user()->user_type_id==5)
                                                <td style="text-align: right;" id="totalQty">{{ $totalDeliveryQty }}</td>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <td style="text-align: right;" id="totalPrice">{{ number_format($totalDeliveryValue,0) }}</td>
                                                <td style="text-align: right;">{{ $totalWastage }}</td>
                                                <td style="text-align: right;">{{ $totalReplaceQty }}</td>
                                                @endif                                          
                                            </tr>
                                            
                                            @php
                                            $com = 0;                                            
                                            @endphp

                                            @if($resultInvoice->total_discount_percentage!=Null)
                                            <tr>
                                                <td colspan="2" align="left">MEMO COMMISSION : {{ $com=$resultInvoice->total_discount_rate }}%</td>                                                
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;">
                                                {{ $resultInvoice->total_discount_percentage }}
                                                </td>
                                                <td>&nbsp;</td>
                                                @if(Auth::user()->user_type_id==5)
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;">&nbsp;</td>
                                                <td>&nbsp;</td>                                            
                                                <td>&nbsp;</td>
                                                @endif                                            
                                            </tr>
                                            @endif

                                        </tbody>
                                        <tfoot class="print-page">
                                            <tr>
                                                <td colspan="2" align="right">NET AMOUNT</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;" id="net_amount">
                                                @if($resultInvoice->total_discount_percentage!=Null)                    
                                                    {{ $netAmount = number_format($totalPrice - ($totalPrice * $com)/100, 0) }}
                                                @else
                                                    {{ $netAmount = number_format($totalPrice,0) }}
                                                @endif   
                                                </td>
                                                <td>&nbsp;</td>
                                                @if(Auth::user()->user_type_id==5)
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
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
                                            <tr>
                                                <td @if(Auth::user()->user_type_id==5) colspan="4" @else colspan="4" @endif> 
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
                                                </td>
                                                <td @if(Auth::user()->user_type_id==5) colspan="6" @else colspan="2" @endif style="text-align: left; padding-left: 40px;">{{ $svwRows->commission.'%' }} 
                                                    {{-- <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$commissionAmount.'/'.$items->catid.'/'.$svwRows->offer_id) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD" class="btn bg-green btn-block btn-sm waves-effect" style="width: 70px;">
                                                    </a> --}}
                                                </td>
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

                                                    $commissionAmount = $svwRows->total_free_value-$valueSum;
                                                @endphp
                                            <tr>
                                                <td @if(Auth::user()->user_type_id==5) colspan="4" @else colspan="4" @endif> 
                                                    @php
                                                    $catQuery = DB::table('tbl_point_wise_value_category')
                                                    ->select('tbl_point_wise_value_category.point_value_id','tbl_point_wise_value_category.categoryid','tbl_product_category.id','tbl_product_category.name')

                                                    ->leftJoin('tbl_product_category','tbl_point_wise_value_category.categoryid','=','tbl_product_category.id')

                                                    ->where('tbl_point_wise_value_category.point_value_id', $svwRows->offer_id)
                                                    ->get();

                                                    foreach($catQuery as $svwcRows)
                                                    {
                                                        echo $catNames = $svwcRows->name.' , ';
                                                        //echo rtrim($catNames,',');
                                                    }
                                                    @endphp
                                                </td>
                                                <td @if(Auth::user()->user_type_id==5) colspan="6" @else colspan="2" @endif style="text-align: left; padding-left: 40px;">{{ $svwRows->commission.'%' }} 
                                                    {{-- <a href="{{ URL('/order-process-valuewise/'.$retailderid.'/'.$routeid.'/'.$svwRows->order_id.'/'.$commissionAmount.'/'.$items->catid.'/'.$svwRows->offer_id) }}">
                                                        <input type="button" name="edit" id="edit" value="ADD" class="btn bg-green btn-block btn-sm waves-effect" style="width: 70px;">
                                                    </a> --}}
                                                </td>
                                            </tr>
                                                @php
                                                }
                                                @endphp
                                            @endif
                                            

                                            @if(sizeof($commissionWiseItem)>0)

                                                <tr style="background:#EEEEEE;">
                                                    <td @if(Auth::user()->user_type_id==5) colspan="10" @else colspan="6" @endif> Exclusive Value Wise Free Items </td>
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
                                                    <td>&nbsp;</td>  
                                                    <td colspan="2"> {{ $items->name }} </td>            
                                                    <td style="text-align: right;">{{ $items->total_free_qty }}</td>
                                                    <td style="text-align: right;">{{ $items->free_value }}</td>
                                                    <td>&nbsp; </td>
                                                    @if(Auth::user()->user_type_id==5)
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>                             
                                                    <td>&nbsp;</td>
                                                    @endif                          
                                                                            
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    
                                                    <td colspan="3"> Total Free</td>            
                                                    <td style="text-align: right;">{{ $cqty }}</td>
                                                    <td style="text-align: right;">{{ $cPrice.'.00' }}</td>
                                                    <td>&nbsp;</td>
                                                    @if(Auth::user()->user_type_id==5)
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>                             
                                                    <td>&nbsp;</td>
                                                    @endif                      
                                                </tr>
                                            @endif
                                            
                                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="background-color: #EEEEEE">
                                                <td colspan="2" align="right"> {{$resultBundleOffersGift->vOfferName}}</td>
                                                <td @if(Auth::user()->user_type_id==5) colspan="3" @else colspan="3" @endif> @if($resultBundleOffersGift->productType==2) {{ $resultBundleOffersGift->giftName }} @else {{ $resultBundleOffersGift->name }} @endif </td>
                                                <td align="right"> {{ $resultBundleOffersGift->free_qty }}</td> 
                                                @if(Auth::user()->user_type_id==5)
                                                <td align="right" colspan="4"></td> 
                                                @endif
                                            </tr>
                                            @endif
                                        </tfoot>                                  
                           </table>
						   
						   
						<!-- @if(Auth::user()->user_type_id==5)   
						    <table width="100%">
                                    <tdead>
                                        <tr>
                                            <td width="70%" height="49" align="left" valign="top">
                                          
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
										  
                                            Opening Balance : {{ number_format($opening_balance,0) }}<br />
                                            Today Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ number_format($netAmount,0) }}<br />
                                            Balance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ number_format($newBalance,0) }}<br /> 
                                           
                                            
                                        
                                            </td>
                                           
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                          <td align="left">&nbsp;</td>
                                          <td align="left">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
						@endif

                        <br/> -->
                             <table width="100%">
                             
                                    <tr>
                                            <td colspan="4">&nbsp;</td>
                                    </tr>
                                     
                                    <tr>
                                           <td> <div style="border: .5px solid #000; width: 200px;"> </div>
                                           <div style="font-size:16px;margin-top:5px;margin-left:53px">Prepared By</div> </td>
                                           
                                           <td align="right"> <div style="border: .5px solid #000; width: 200px;" > </div>
                                           <div style="font-size:16px;margin-top:5px;margin-right:66px">Signed By</div> </td>
                                    </tr>
                                       
                             </table>		
						   
                        </div>



                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printCount({{ $resultInvoice->order_id }});printReport()">
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
