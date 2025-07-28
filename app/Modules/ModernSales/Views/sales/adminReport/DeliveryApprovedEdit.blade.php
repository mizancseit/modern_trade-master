@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Delivery
                     </small>
                 </h2>
             </div>
         </div>
     </div>
 </div>

 @if(Session::has('success'))
 <div class="alert alert-success">
    {{ Session::get('success') }}                        
</div>
@endif

<form action="{{ URL('/orderDelivery-edit-submit') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)

                <div class="header">
                    <div class="row">
                        <div class="col-sm-8">
                            <span>  
                                PO No &nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->po_no }}<br />
                                Customer Code :&nbsp; {{ $customerInfo->sap_code }}<br />
                                
                                Outlet Name  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}
                                <br /> 
                                Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                    @if($customerInfo->route_id==1)
                                    Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                    @endif
                            </span>
                        </div>

                        <div class="col-sm-4">
                            <span>

                                Collected By &nbsp; : {{$resultInvoice->display_name}}<br />
                                Sales Order No : {{ $resultInvoice->order_no }}<br />
                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->order_date)) }}

                            </span>
                        </div>
                    </div>                                                           
                </div>

                <div class="header">
                    <h2>Sales Order</h2>                            
                </div>

                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Product Name</th>
                                <th>Order Qty</th>
                                <th>Order Value</th>
                                <th>Delivery Qty</th>
                                <th>Delivery Value</th>                   
                            </tr>
                        </thead>

                        <tbody>

                            @if(sizeof($resultCartPro)>0)
                            @php
                            $serial   = 1;
                            $count    = 1;
                            $subTotal = 0;
                            $totalQty = 0;
                            $totalPrice = 0;

                            $totalDeliveryQty = 0;
                            $totalDeliveryValue = 0;
                            @endphp
                            @foreach($resultCartPro as $items)
                            @php
                            $resultComm = DB::table('mts_categroy_wise_commission')
                            ->select('order_commission_value', 'commission')
                            ->where('mts_categroy_wise_commission.order_id',$items->order_id)
                            ->where('mts_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                            @endphp                                        
                            <tr>
                                <td></td>
                                <td colspan="6">{{ $items->catname }} <span class="pull-right">Discount: {{$resultComm->commission}}, Value: {{$resultComm->order_commission_value}}</span></td>
                            </tr>

                            @php 
                            $itemsCount = 1;
                            $reultPro  = DB::table('mts_order_details')
                            ->select('mts_order_details.order_det_id','mts_order_details.cat_id','mts_order_details.order_id','mts_order_details.order_qty','mts_order_details.order_total_value','mts_order_details.deliverey_qty','mts_order_details.delivery_value','mts_order_details.product_id','mts_order_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.party_id','tbl_product.id','tbl_product.name AS proname')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
                            ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id') 
                            ->where('mts_order.order_status','Delivered') 
                            ->where('mts_order.ack_status','Pending') 
                            ->where('mts_order.fo_id',$foMainId)                        
                            ->where('mts_order_details.order_id',$DeliveryMainId)
                            ->where('mts_order_details.cat_id', $items->catid)    
                            ->get();
                            //dd($reultPro);
                            @endphp
                            @foreach ($reultPro as $itemsPro)
                            @php
                            $subTotal += $itemsPro->order_total_value;
                            $totalQty += $itemsPro->order_qty;
                            $totalPrice += $itemsPro->order_total_value;
                            $totalDeliveryQty  += $itemsPro->deliverey_qty;
                            $totalDeliveryValue += $itemsPro->delivery_value;

                            @endphp

                            <tr>
                                <td>{{ $serial }}</td>
                                <td></td>
                                <td>{{ $itemsPro->proname }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->order_qty,0) }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->deliverey_qty,0) }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->delivery_value,2) }}</td>

                                <input type="hidden" id="change_prod_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}" >

                                <input type="hidden" name="product_id[]" id="product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >

                            </tr>
                            @php
                            $serial ++;
                            @endphp
                            @endforeach                                            
                        </tr>

                        @endforeach
                        @endif

                        <tr>
                            <th colspan="3" style="text-align: right;">Total</th>
                            <th style="text-align: center;">{{ number_format($totalQty,2) }}</th>
                            <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th>
                            <th style="text-align: right;">{{ $totalDeliveryQty }}</th>
                            <th style="text-align: right;">{{ number_format($totalDeliveryValue,2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" style="text-align: right;">Total Discount</th>
                            <th>&nbsp;</th>
                            <th style="text-align: center;">@if(sizeof($orderCommission)>0)
                                {{ number_format($orderCommission->commission,2) }} @else {{'0.00'}}  @endif
                            </th>
                           
                        </tr>
                        <tr>
                            <th colspan="5" style="text-align: right;">Net Amount</th>
                            <th>&nbsp;</th>
                            <th style="text-align: center;">@if(sizeof($orderCommission)>0)
                                {{ number_format($totalPrice-$orderCommission->commission,2) }} @else {{'0.00'}}  @endif
                            </th>
                           
                        </tr>

                    </tbody>

                </table>
                <p></p>
                <div class="row" style="text-align: center;">
                    <div class="col-sm-3">
                        <a href="{{ URL('/mts-approved-delivery/'.$resultInvoice->order_id.'/'.$resultInvoice->customer_id.'/'.'yes') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Approved</button>
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <a href="{{ URL('/mts-approved-delivery/'.$resultInvoice->order_id.'/'.$resultInvoice->customer_id.'/'.'no') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        </a>
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                <input type="hidden" name="retailderid" id="retailderid" value="{{ $resultInvoice->party_id }}">
                <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">

            </div>

            @endif
        </div>
    </div>
</div>

</form>

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
@endsection
