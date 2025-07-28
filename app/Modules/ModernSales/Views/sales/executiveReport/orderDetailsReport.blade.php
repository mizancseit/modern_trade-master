@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Order 
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
                                            Customer Name &nbsp;:&nbsp; {{ $customerInfo->name }}<br />
                                            Customer Code &nbsp;:&nbsp; {{ $customerInfo->sap_code }}<br />
                                            
                                            Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}
                                            <br />
                                            
                                           Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                            @if($customerInfo->route_id==1)
                                            Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                            @endif
                                        </th>
                                        <th width="55%" align="left" valign="top" style="font-weight: normal;vertical-align:top">                                       
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
                                        <th class="headerTable" width="10%" valign="middle">Products Group</th>
                                        <th class="headerTable" width="10%" valign="middle">Products Code</th>
                                        <th class="headerTable" width="30%" valign="middle">Product Name</th>
                                        <th class="headerTable" width="7%" valign="middle">Unit Price</th>
                                        <th class="headerTable" width="7%" valign="middle">Order Qty</th>                                        
                                        <th class="headerTable" width="8%" valign="middle">Order Value</th>                                      
                                        <th class="headerTable" width="8%" valign="middle">Net Value</th>                                      
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
                                    @php
                                    $resultComm = DB::table('mts_categroy_wise_commission')
                                    ->select('order_commission_value', 'commission')
                                    ->where('mts_categroy_wise_commission.order_id',$orderMainId)
                                    ->where('mts_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                                    @endphp                                     
                                    <tr>
                                        <th></th>
                                        <th colspan="6">{{ $items->catname }} 
                                            @if(sizeof($resultComm)>0) <span class="pull-right">Discount: {{$resultComm->commission}} %, Value: {{$resultComm->order_commission_value}}</span>@endif
                                        </th>
                                    </tr>
                                    @php 


                                    $itemsCount = 1;
                                    $reultPro  = DB::table('mts_order_details')
                                    ->select('mts_order_details.p_unit_price','mts_order_details.deliverey_qty','mts_order_details.order_det_id','mts_order_details.cat_id','mts_order_details.order_id','mts_order_details.order_qty','mts_order_details.order_total_value','mts_order_details.product_id','mts_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.party_id','tbl_product.id','tbl_product.name AS proname','tbl_product.sap_code')
                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
                                    ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                                    ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
                                    //->where('mts_order.order_status','Delivered')
                                    ->where('mts_order_details.order_id',$orderMainId)
                                    ->where('mts_order_details.cat_id', $items->catid)    
                                    ->get();
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
                                        <td class="rowTableRight"></td>
                                        <td class="rowTableRight">{{ $itemsPro->sap_code }}</td>
                                        <td class="rowTableLeft">{{ $itemsPro->proname }}</td>
                                        <td class="rowTableRight"> {{ number_format($itemsPro->p_unit_price,2) }} </td>
                                        <td class="rowTableRight">{{ number_format($itemsPro->order_qty,0) }}</td>
                                        <td class="rowTableRight">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,2) }}</td>
                                        <td class="rowTableRight">{{ number_format($itemsPro->order_qty * $itemsPro->p_unit_price,2) }}</td>
                                        


                                    </tr>

                                    @php
                                    $serial ++;
                                    @endphp
                                    @endforeach                                            
                                    </tr>

                                   {{--  <tr>
                                        <th></th>
                                        <th colspan="6"></th>
                                    </tr> --}}
                               



                                @php
                                @endphp
                                @endforeach
                                <tr>
                                    <th colspan="5"  class="rowTableLeft">Total Amount</th>
                                    <th class="rowTableRight">{{number_format($totalQty,0)}}</th>
                                    <th class="rowTableRight">{{number_format($totalPrice,2)}}</th>                                 
                                                                                
                                </tr>

                                <tr>
                                    <th colspan="5">Discount</th>
                                    <th style="text-align: right;">
                                    </th>
                                    <th style="text-align: right;">@if(sizeof($orderCommission)>0)
                                        {{ number_format($orderCommission->commission,2) }} @else {{'0.00'}}  @endif
                                    </th>
                                    
                                </tr>
                                <tr>
                                    <th colspan="5"  class="rowTableLeft">Net Total</th>
                                    <th class="rowTableRight">{{number_format($totalQty,0)}}</th>
                                    <th class="rowTableRight">{{number_format($totalPrice-$orderCommission->commission,2)}}</th>    
                                    
                                                                               
                                </tr>

                                @endif



                            </table>

                            
                            <table width="100%">
                                <tr>
                                        <td colspan="2" height="60"></td>
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
