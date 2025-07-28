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
                                            <th width="70%" height="49" align="left" valign="top">
                                            {{ $resultDistributorInfo->first_name }} <br />
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
                        <table class="table table-bordered">
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
                                    $totalDeliveryQty = 0;
                                    $totalDeliveryPrice = 0;
                                    @endphp
                                    @foreach($resultCartPro as $items)                                       
                                    <tr>
                                        <th></th>
                                        <th>{{ $items->catname }}</th>

                                        @php 
                                        $itemsCount = 1;
                                $reultPro  = DB::table('tbl_order_details')
                                            ->select('tbl_order_details.free_qty','tbl_order_details.delivered_qty','tbl_order_details.replace_delivered_qty','tbl_order_details.order_det_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
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

                                        @endphp

                                        <tr>
                                        <th>{{ $serial }}</th>
                                        <th></th>
                                        <th>{{ $itemsPro->proname }}</th>
                                        <th style="text-align: right;">{{ $itemsPro->order_qty }}</th>
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
                                            ->select('fq.type','fq.auto_order_no','fq.catid','fq.product_id','fq.total_free_qty','tbl_product.id','tbl_product.name AS proname','fq.status')
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
                                        <th colspan="3" align="right">Sub Total</th>
                                        <th style="text-align: right;">{{ $totalQty }}</th>
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
                                        <th>&nbsp;</th>                                            
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" align="right">NET AMOUNT</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th style="text-align: right;" id="net_amount">
                                        @if(sizeof($reultProRate)>0)                                                
                                            {{ number_format($totalDeliveryPrice - ($totalDeliveryPrice * $com)/100, 0) }}
                                        @else
                                            {{ number_format($totalDeliveryPrice,0) }}
                                        @endif   
                                        </th>
                                        <th>&nbsp;</th>                                            
                                        <th>&nbsp;</th>                                           
                                    </tr>

                                    @if(sizeof($resultBundleOffersGift)>0)
                                    <tr style="background-color: #EEEEEE">
                                        <th colspan="2" align="right"> {{$resultBundleOffersGift->vOfferName}}</th>
                                        <th colspan="8"> @if($resultBundleOffersGift->productType==2) {{ $resultBundleOffersGift->giftName }} @else {{ $resultBundleOffersGift->name }} @endif </th>     
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
											$opening_balance = $retCredLedgerData[0]->retailer_opening_balance;;
											$netAmount 		 = $retCredLedgerData[0]->retailer_invoice_sales;
											$newBalance =  $retCredLedgerData[0]->retailer_balance;
										   
										 } else {
										
											$retailerData = DB::select("SELECT opening_balance FROM tbl_retailer 
											 WHERE retailer_id = '".$resultInvoice->retailer_id."'");
											
											if(sizeof($retailerData)>0)
											{
											  $opening_balance = $retailerData[0]->opening_balance;
											} else {
											  $opening_balance = 0;
											}
											
											
											$retCollection = 0;
											
											
											if(sizeof($reultProRate)>0):   
												$netAmount = $totalDeliveryPrice - ($totalDeliveryPrice * $com)/100;
											else:
												$netAmount = $totalDeliveryPrice;
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
							
							<div class="row" style="text-align: center;">
									<div class="col-sm-12">
										<button type="button" class="btn bg-red waves-effect" onclick="printReport()">
											<i class="material-icons">print</i>
											<span>PRINT</span>
										</button>
									</div>
								</div>
							
                            {{-- <p></p>
                            <div class="row" style="text-align: center;">
                                <div class="col-sm-3">
                                    <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                </div>
                            </div> --}}
							
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