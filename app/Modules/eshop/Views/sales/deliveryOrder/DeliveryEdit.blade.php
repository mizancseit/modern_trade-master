@extends('eshop::masterPage')
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

<form action="{{ URL('/eshop-orderDelivery-edit-submit') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)

                <div class="header">
                    <div class="row">
                        <div class="col-sm-8">
                            <span>
                            PO No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->po_no }}<br />
                            Customer Code &nbsp;:&nbsp; {{ $customerInfo->sap_code }}<br />
                            
                            Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}
                            <br />
                            
                           Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                            @if($customerInfo->route_id==1)
                            Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                            @endif
                            </span>
                        </div>

                        <div class="col-sm-4">
                            <span>
                                Sales Order No : {{ $resultInvoice->order_no }}<br />
                                Collected By &nbsp; : {{$resultInvoice->display_name}}<br /> 
                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->order_date)) }}

                            </span>
                        </div>
                    </div>                                                           
                </div>

                <div class="header">
                    <div class="row">
                         <div class="col-lg-3">
                            <h2>DELIVERY ENTRY</h2>  
                         </div>
                         <div class="col-lg-2 text-right" style="padding-top: 8px;"><h2>Credit Limit.</h2> </div>

                         @if($creditSummery>0)
                         <div class="col-lg-2 text-left">
                         <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-success" value="{{ $creditSummery}}" readonly="">  </div>
                         @else
                         <div class="col-lg-2 text-left">
                         <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-danger" value="{{ $creditSummery}}" readonly="">  </div>
                         @endif

                    </div> 
                                                
                </div>

                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Product Name</th>
                                <th>Delivery</th>
                                <th>Value</th>
                                <th>Item Discount %</th>
                                <!-- <th>R.Delivery</th>
                                <th>R.Value</th>  -->                  
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
                            $totalDiscount = 0;
                            @endphp
                            @foreach($resultCartPro as $items)   
                            @php
                            $resultComm = DB::table('eshop_categroy_wise_commission')
                            ->select('order_commission_value', 'commission')
                            ->where('eshop_categroy_wise_commission.order_id',$items->order_id)
                            ->where('eshop_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                            @endphp                                     
                            <tr>
                                <th></th>
                                <th colspan="4">{{ $items->catname }} </th>
                            </tr>

                            @php 
                            $itemsCount = 1;
                            $reultPro  = DB::table('eshop_order_details')
                            ->select('eshop_order_details.order_det_id','eshop_order_details.item_discount','eshop_order_details.cat_id','eshop_order_details.order_id','eshop_order_details.order_qty','eshop_order_details.order_total_value','eshop_order_details.product_id','eshop_order_details.order_qty','eshop_order_details.p_unit_price','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.party_id','eshop_product.id','eshop_product.name AS proname')

                            //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                            //->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')

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
                            if( $itemsPro->item_discount > 0){
                                $totalDiscount += $itemsPro->order_total_value * $itemsPro->item_discount /100;
                            }                           

                            @endphp

                            <tr>
                                <th>{{ $serial }}</th>
                                <th></th>
                                <th>{{ $itemsPro->proname }}</th>
                                <th style="text-align: center;">{{ $itemsPro->order_qty }}</th>
                                <th style="text-align: center;">{{ number_format($itemsPro->order_total_value,0) }}</th>
                                <th style="text-align: center;">{{ number_format($itemsPro->item_discount,0) }}</th>

                                <input type="hidden" id="change_prod_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}" >
                               <!--  <th style="text-align: right;">

                                    <input type="number" class="form-control" id="changeQty{{$serial}}" name="qty[]" value="{{ $itemsPro->order_qty }}" maxlength="3" style="width: 80px;" onkeyup="addChange({{$serial}})">
                                </th>



                                <th>
                                    <input type="text" class="form-control" id="changeValue{{$serial}}" name="price[]" value="{{$itemsPro->order_total_value}}" maxlength="8" style="width: 80px;"  readonly="">
                                </th> -->

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
                            <th colspan="3" align="right">Total</th>
                            <th style="text-align: center;">{{ $totalQty }}</th>
                            <th style="text-align: center;">{{ number_format($totalPrice,0) }}</th>
                            <!-- <th style="text-align: center;" id="totalQty">{{ $totalQty }}</th> -->
                            <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                            <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                            <!-- <th style="text-align: center;" id="totalPrice">{{ number_format($totalPrice,0) }}
                                <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                            </th> -->

                        </tr>
                        <tr>
                            <th colspan="4">Total Discount</th>
                            <th style="text-align: right;">{{$totalDiscount}} </th> 
                        </tr> 
                        <tr>
                            <th colspan="4">Grand Total</th>
                            <th style="text-align: right;">@if(sizeof($orderCommission)>0)
                                {{ number_format($totalPrice-$orderCommission->commission,2) }} @else {{'0.00'}}  @endif
                            </th>
                            <!-- <th>&nbsp;</th>
                            <th>&nbsp;</th> -->
                        </tr>

                    </tbody>

                </table>
                <p></p>
                <div class="row" style="text-align: center;">
                    {{-- <div class="col-sm-3">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                    </div> --}}
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