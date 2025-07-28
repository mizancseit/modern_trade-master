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
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                         <div class="body" id="printMe">
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


											Order No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_no }}
                                            <p></p>
                                            Collected By : {{ $resultFoInfo->first_name }} <br />
                                            Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }} <br />
                                            Order Date &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_date }}

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

                                <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                  <thead>
                                    <tr>
                                      <th>SL</th>
                                      <th>Product Group</th>
                                      <th>Product Name</th>
                                      <th>Order</th>
                                      <th>Value</th>
                                      <th>Free</th>
                                      <th>Delivery</th>
                                      <th>Delivery Value</th>
                                      <th>Wastage</th>                                            
                                      <th>Replace Delivery</th>
                                    </tr>
                                  </thead>

                                  <tbody>
                                            
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
                                                    ->select('tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.free_qty','tbl_order_details.p_unit_price','tbl_order_details.delivered_qty','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                                                                        
                                                    ->where('tbl_order.fo_id', Auth::user()->id)                        
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

                                                @endphp

                                                <tr>
                                                    <th>{{ $serial }}</th>
                                                    <th></th>
                                                    <th>{{ $itemsPro->proname }}</th>
                                                    <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
                                                    <th style="text-align: right;">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,0) }}</th>
                                                    <th style="text-align: right;">@if($itemsPro->free_qty!=null) {{ $itemsPro->free_qty }} @else - @endif</th>
                                                    <th style="text-align: right;">@if($itemsPro->delivered_qty!=null) {{ $itemsPro->delivered_qty }} @else - @endif</th>
                                                    <th style="text-align: right;" id="rowPrice{{ $serial }}">@if($itemsPro->delivered_qty!=null) {{ number_format($itemsPro->delivered_qty * $itemsPro->p_unit_price,0) }} @else - @endif
                                                    </th>                                                
                                                    <th style="text-align: right;">@if($itemsPro->wastage_qty==null) - @else {{ $itemsPro->wastage_qty }} @endif</th>

                                                    <th style="text-align: right;">@if($itemsPro->replace_delivered_qty==null) - @else {{ $itemsPro->replace_delivered_qty }} @endif</th>
                                                </tr>

                                                @php
                                                $serial ++;
                                                @endphp
                                                @endforeach                                            
                                            </tr>

                                                @php                                             
                                                    $reultRegularGift  = DB::table('tbl_order_free_qty AS fq')
                                                    ->select('fq.type','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.type','R')      
                                                    ->where('fq.auto_order_no',$resultInvoice->auto_order_no)
                                                    ->where('fq.catid', $items->catid)    
                                                    //->where('fq.product_id', $itemsPro->id)    
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
                                                    <th style="text-align: right;"> </th>     
                                                    <th style="text-align: right;"> </th>       
                                                    <th style="text-align: right;"> </th>
                                                    </tr>                                                                                           
                                                @endforeach
                                            
                                            @endforeach
                                            @endif

                                            <tr>
                                                <th colspan="3" align="right">Sub Total</th>
                                                <th style="text-align: right;">{{ $totalQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalPrice,0) }}</th>
                                                <th style="text-align: right;">{{ $totalFreeQty }}</th>
                                                <th style="text-align: right;" id="totalQty">{{ $totalDeliveryQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <th style="text-align: right;" id="totalPrice">{{ number_format($totalDeliveryValue,0) }}</th>
                                                <th style="text-align: right;">{{ $totalWastage }}</th>
                                                <th style="text-align: right;">{{ $totalReplaceQty }}</th>                                            
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
                                                <th style="text-align: right;">
                                                @if(sizeof($reultProRate)>0)                                              
                                                    {{ number_format(($totalPrice * $com)/100, 0) }}
                                                @else
                                                    0
                                                @endif
                                                </th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;">&nbsp;</th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                            
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" align="right">NET AMOUNT</th>
                                                <th>&nbsp;</th>
                                                <th style="text-align: right;" id="net_amount">
                                                @if(sizeof($reultProRate)>0)                    
                                                    {{ number_format($totalPrice - ($totalPrice * $com)/100, 0) }}
                                                @else
                                                    {{ number_format($totalPrice,0) }}
                                                @endif   
                                                </th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>                                            
                                                <th>&nbsp;</th>                                           
                                            </tr>
                                            @if(sizeof($resultBundleOffersGift)>0)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="10" align="right"> Bundle Offer</th>
                                            </tr>

                                            @php
                                            $bs =1;
                                            @endphp

                                            @foreach($resultBundleOffersGift as $gifts)
                                            <tr style="background-color: #F3F3F3">
                                                <th colspan="2" align="right"> {{ $bs }} </th>
                                                <th colspan="3"> {{ $gifts->name }} </th>
                                                <th style="text-align: right;"> {{ $gifts->free_qty }} </th>
                                                <th colspan="4"> </th>     
                                            </tr>
                                            @php
                                            $bs ++;
                                            @endphp
                                            @endforeach

                                            @endif
                                        </tfoot>                                  
                                </table>
                            </div>
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