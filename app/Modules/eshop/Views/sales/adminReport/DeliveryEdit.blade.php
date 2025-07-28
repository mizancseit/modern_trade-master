@extends('eshop::masterPage')
@section('content')
<style type="text/css">
    .warning{
        background-color: #ffd90f !important ;
    }
</style>
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

    <!-- <form action="{{ URL('/eshop-not-approved-remarks-submit') }}" method="POST"> -->
    <form action="{{ URL('/eshop-email-approval/'.$DeliveryMainId.'/'.$foMainId.'/'.$resultInvoice->party_id) }}" method="POST">
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
                                    Invoice No : {{ $resultInvoice->order_no }}<br />
                                    Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->order_date)) }}

                                </span>
                            </div>
                        </div>                                                           
                    </div>
                    @php
                    $eapproval_status = 0;
                    @endphp
                    <div class="header">
                        <div class="row">
                           {{--  <div class="col-lg-3">
                                <h2>Sales Order</h2>  
                            </div> --}}
                            {{-- <div class="col-lg-2 text-right" style="padding-top: 8px;"><h2>Credit Limit.</h2> </div>

                            @if($creditSummery>0)
                            <div class="col-lg-2 text-left">
                                <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-success" value="{{ $creditSummery}}" readonly="">  </div>
                                @else
                                <div class="col-lg-2 text-left">
                                    <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-danger" value="{{ $creditSummery}}" readonly="">  </div>
                                    @endif

                                </div> 

                            </div> --}}

                            <div class="body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL  </th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>SAP code</th>
                                            <th>DP Price</th>
                                            <th>Unite Price</th> 
                                            <th>Order Qty</th>
                                            <th>Subtotal</th>   
                                            <th>Item Discount %</th>                  
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
                                        $item_discount = 0;
                                        $totalUnitPrice = 0;
                                        $totaldpPrice = 0;
                                        @endphp
                                        @foreach($resultCartPro as $items)
                                        @php
                                        $resultComm = DB::table('eshop_categroy_wise_commission')
                                        ->select('order_commission_value', 'commission')
                                        ->where('eshop_categroy_wise_commission.order_id',$items->order_id)
                                        ->where('eshop_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                                        @endphp                                        
                                        <tr>
                                            <td></td>
                                            <td colspan="6">{{ $items->catname }} 
                                                </td>
                                        </tr>

                                        @php 
                                        $itemsCount = 1;
                                        $reultPro  = DB::table('eshop_order_details')
                                        ->select('eshop_order_details.order_det_id','eshop_order_details.cat_id','eshop_order_details.order_id','eshop_order_details.order_qty','eshop_order_details.order_total_value','eshop_order_details.product_id','eshop_order_details.order_qty','eshop_order_details.p_unit_price','eshop_order_details.item_discount','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.party_id','eshop_product.id','eshop_product.name AS proname' , 'eshop_product.distri','eshop_product.sap_code')

                                        //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                                        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                                        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')

                                        ->where('eshop_order.order_status','Confirmed')                        
                                        ->where('eshop_order.fo_id',$foMainId)                        
                                        ->where('eshop_order_details.order_id',$DeliveryMainId)
                                        ->where('eshop_product.category_id', $items->catid)    
                                        ->get();
                                        //dd($reultPro);
                                        @endphp
                                        @foreach ($reultPro as $itemsPro)
                                        @php
                                        $subTotal += $itemsPro->order_total_value;
                                        $totalQty += $itemsPro->order_qty;
                                        $totalPrice += $itemsPro->order_total_value;
                                        $totaldpPrice += $itemsPro->distri;
                                        $totalUnitPrice += $itemsPro->p_unit_price;
                                        if($itemsPro->item_discount > 0){
                                            $item_discount += $itemsPro->order_total_value * $itemsPro->item_discount/100;
                                        }

                                        if( $itemsPro->p_unit_price < $itemsPro->distri ){
                                            $eapproval_status =  1 ;
                                            $warning = 1;
                                        }else{
                                            $warning = 0;
                                        }
                                             
                                        @endphp

                                        <tr  class="{{ $itemsPro->p_unit_price < $itemsPro->distri ? 'alert-amount' : '' }}" >
                                            <td>{{ $serial }}</td>
                                            <td></td>
                                            <td>{{ $itemsPro->proname }}</td>
                                            <td>{{ $itemsPro->sap_code }}</td>
                                            <td >{{ $itemsPro->distri }}</td>
                                            <td <?php if($warning == 1){ ?> class="warning" <?php } ?>>{{ $itemsPro->p_unit_price }}</td>
                                            <td style="text-align: center;">{{ number_format($itemsPro->order_qty,0) }}</td>
                                            <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td>
                                            <td style="text-align: center;">{{ $itemsPro->item_discount }}</td>

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
                                        <th colspan="4" style="text-align: right;">Total</th>
                                        <th style="text-align: center;">{{ number_format($totaldpPrice,2) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalUnitPrice,2) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalQty,0) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th>
                                        <th style="text-align: center;"> </th>
                                    </tr>
                                    @if(sizeof($resultInvoice)>0)
                                    <tr>
                                        <th colspan="6" style="text-align: right;">Commission</th>
                                        <th>&nbsp;</th>
                                        <th style="text-align: center;">
                                            {{ $item_discount }}  
                                        </th>
                                        <th style="text-align: center;"> </th>
                                    </tr>
                                   
                                    <tr>
                                        <th colspan="6" style="text-align: right;">Net Amount</th>
                                        <th>&nbsp;</th>
                                        <th style="text-align: center;">@if(sizeof($orderCommission)>0)
                                            {{ number_format($totalPrice-$orderCommission->commission,2) }} @else {{'0.00'}}  @endif
                                        </th>
                                        <th style="text-align: center;"> </th>
                                    </tr>
                                     @endif
                                    @php
                                    $remarksResult = DB::table('eshop_remarks')
                                    ->where('reference_id',$resultInvoice->order_id)
                                    ->where('customar_id',$customerInfo->customer_id)
                                    ->where('party_id',$resultInvoice->party_id)
                                    ->where('remarks_type','order') 
                                    ->first();

                                    @endphp

                                    @if(sizeof($remarksResult)>0)
                                    <tr>
                                        <td colspan="2" style="text-align: center;">Remarks</td>

                                        <td colspan="4">{{$remarksResult->remarks}}</td>  
                                    </tr> 
                                    @else

                                    <tr>
                                        <td colspan="2" style="text-align: center;">Remarks</td>
                                        <td  colspan="4" style="text-align: center;"><textarea class="form-control" rows="2" name="remarks" id="remarks"></textarea></td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>
                            <p></p>
                            <div class="row" style="text-align: center;"> 
                                @if($eapproval_status == 1)
                                <div class="col-sm-3 " >
                                    <!-- <a href="{{ URL('/eshop-email-approval/'.$DeliveryMainId.'/'.$foMainId.'/'.$resultInvoice->party_id) }}" class="btn bg-pink btn-block btn-lg waves-effect">
                                        Send mail for approval 
                                    </a> -->
                                    <input type="submit" value="Send mail for approval"  class="btn bg-pink btn-block btn-lg waves-effect">
                                </div>
                                @else
                                <div class="col-sm-3">
                                    <a href="{{ URL('/eshop-approved-order/'.$resultInvoice->order_id.'/'.$resultInvoice->party_id.'/'.'yes') }}" class="btn bg-pink btn-block btn-lg waves-effect">
                                        Approved  
                                    </a>
                                </div>
                                @endif
                               <!--  <div class="col-sm-3">

                                    <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>

                                </div> -->
                            </div>
                            <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                            <input type="hidden" name="party_id" id="party_id" value="{{ $resultInvoice->party_id }}">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customerInfo->customer_id }}">
                            <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                            <input type="hidden" name="remarks_type" id="remarks_type" value="order">

                        </div>

                        @endif
                    </div>
                </div>
            </div>

        </form>

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