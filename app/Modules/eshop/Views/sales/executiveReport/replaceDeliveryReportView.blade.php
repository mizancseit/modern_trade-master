@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
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

                    @if(!empty($resultCartPro))                    
                    <div class="body">

                            <table class="table table-bordered" width="100%" style="margin-top:5px;font-family: arial;font-size:8;font-weight: normal;">
                                <thead>
                                    <tr>
                                        <th width="45%" align="left" valign="top" style="font-weight: normal;vertical-align:top">
                                            PO No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->po_no }}<br />
                                            Customer Code&nbsp;&nbsp;&nbsp; :&nbsp; {{ $customerInfo->sap_code }}<br /> 
                                            Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}<br />
                                            Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                            @if($customerInfo->route_id==1)
                                            Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                            @endif
                                        </th>
                                        <th width="55%" align="left" valign="top" style="font-weight: normal;vertical-align:top">                                       
                                            Replace No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultInvoice->replace_no }} <br />
                                            Collected By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->display_name }} <br />
                                            Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultInvoice->replace_date }}
                                            <br />
                                            
                                            Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{ $resultFoInfo->cell_phone }} <br />


                                        </th>
                                    </tr>
                                </thead>                            
                            </table>

                            <table class="table table-bordered" style="margin-top:-10px;">
                                <thead >
                                    <tr>
                                        <th class="headerTable" width="3%" valign="middle">Sl.</th>
                                        <th></th>
                                        <th class="headerTable" width="3%" valign="middle">Product Code</th>
                                        <th class="headerTable" width="37%" valign="middle">Item</th>
										
                                        {{-- <th class="headerTable" width="7%" valign="middle">Unit Price</th> --}}
                                        <th class="headerTable" width="7%" valign="middle">Order Qty</th>                                        
                                        {{-- <th class="headerTable" width="8%" valign="middle">Order Value</th> --}}
                                        <th class="headerTable" width="8%" valign="middle">Delivery Qty</th>{{-- 
                                        <th class="headerTable" width="9%" valign="middle">Delivery Value</th>  --}}                                         
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
                                        <th colspan="3"></th>
                                    </tr>
                                    @php 


                                    $itemsCount = 1;
                                    $reultPro  = DB::table('eshop_replace_details')
                                    ->select('eshop_replace_details.p_unit_price','eshop_replace_details.deliverey_qty','eshop_replace_details.replace_det_id','eshop_replace_details.cat_id','eshop_replace_details.replace_id','eshop_replace_details.order_qty','eshop_replace_details.order_total_value','eshop_replace_details.product_id','eshop_replace_details.p_unit_price','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_replace.replace_id','eshop_replace.fo_id','eshop_replace.order_status','eshop_replace.party_id','eshop_product.id','eshop_product.name AS proname','eshop_product.sap_code')
                                    ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_replace_details.cat_id')
                                    ->join('eshop_replace', 'eshop_replace.replace_id', '=', 'eshop_replace_details.replace_id')
                                    ->join('eshop_product', 'eshop_product.id', '=', 'eshop_replace_details.product_id')
                                    //->where('eshop_replace.order_status','Delivered')
                                    ->where('eshop_replace_details.replace_id',$orderMainId)
                                    ->where('eshop_replace_details.cat_id', $items->catid)    
                                    ->get();
                                    //dd($reultPro);
                                    @endphp
                                    @foreach ($reultPro as $itemsPro)
                                    @php
                                    $subTotal += $itemsPro->order_total_value;
                                    $totalQty += $itemsPro->order_qty;
                                    $totalDeliveryQty += $itemsPro->deliverey_qty;
                                    $totalDeliveryPrice += $itemsPro->deliverey_qty* $itemsPro->p_unit_price;
                                    $totalPrice += $itemsPro->order_qty * $itemsPro->p_unit_price;
                                    $totalPerUnitPrice += $itemsPro->p_unit_price;

                                    @endphp

                                    <tr>
                                        <td class="rowTableCenter">{{ $serial }}</td>
                                        <td></td>
                                        <td>{{ $itemsPro->sap_code }}</td>
                                        <td class="rowTableLeft">{{ $itemsPro->proname }}</td>
                                        {{-- <td class="rowTableRight"> {{ number_format($itemsPro->p_unit_price,2) }} </td> --}}
                                        <td class="rowTableRight">{{ number_format($itemsPro->order_qty,0) }}</td>
                                        {{-- <td class="rowTableRight">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,2) }}</td> --}}
                                        <td class="rowTableRight">{{ number_format($itemsPro->deliverey_qty,0) }}</td>
                                        {{-- <td class="rowTableRight" id="rowPrice{{ $serial }}">{{ number_format($itemsPro->deliverey_qty * $itemsPro->p_unit_price,2) }}</td> --}}


                                    </tr>

                                    @php
                                    $serial ++;
                                    @endphp
                                    @endforeach                                            
                                </tr> 
                                @php
                                @endphp
                                @endforeach
                                <tr>
                                    <th colspan="4"  class="rowTableLeft">Total Amount</th>
                                    <th class="rowTableRight">{{number_format($totalQty,0)}}</th>
                                    {{-- <th class="rowTableRight">{{number_format($totalPrice,2)}}</th> --}}                                 
                                    <th class="rowTableRight">
                                        {{number_format($totalDeliveryQty,0)}}
                                    </th>
                                   {{--  <th class="rowTableRight">{{number_format($totalDeliveryPrice,2)}}</th>   --}}                                          
                                </tr> 

                                @endif



                            </table>

                            
                            <table width="100%">
								<tr>
									<td colspan="2" height="60">&nbsp;</td>
								</tr>

                                <tr>
                                    <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                                        <div style="text-align: center; font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-left:0px">Prepared By</div>
                                    </td>


                                    <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                                       <div style="text-align: center; font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:0px">Received By</div> 
                                   </td>
                               </tr>

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