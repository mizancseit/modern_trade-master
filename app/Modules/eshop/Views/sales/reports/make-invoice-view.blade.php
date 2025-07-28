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
<form action="{{ URL('/eshop-make-invoice-stock-out') }}" method="POST">
    {{ csrf_field() }} 
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($itemsGroup)>0)

                <div class="header">
                    <div class="row">
                        <div class="col-sm-8">
                            <span>  
                                PO No &nbsp;&nbsp;&nbsp;&nbsp;: {{ $invice->po_no }}<br />
                                Customer Code :&nbsp; {{ $invice->customer->sap_code }}<br />
                                
                                Outlet Name  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $invice->party->name }}
                                <br /> 
                                Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $invice->customer->address }}<br />
                                    @if($invice->route_id==1)
                                    Shiping Address&nbsp;:&nbsp; {{ $invice->customer->address }}
                                    @endif
                            </span>
                        </div>

                        <div class="col-sm-4">
                            <span>

                                Collected By &nbsp; : {{$invice->userfo->display_name}}<br />
                                Sales Order No : {{ $invice->order_no }}<br />
                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($invice->order_date)) }}

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
                                <th>Qty</th>
                                <th>Subtotal</th>                
                            </tr>
                        </thead>

                        <tbody>

                            @if(sizeof($itemsGroup)>0)
                            @php
                            $serial   = 1;
                            $count    = 1;
                            $subTotal = 0;
                            $totalQty = 0;
                            $totalPrice = 0;

                            $totalDeliveryQty = 0;
                            $totalDeliveryValue = 0;
                            @endphp
                            @foreach($itemsGroup as $items)
                            @php
                            $resultComm = DB::table('eshop_categroy_wise_commission')
                            ->select('order_commission_value', 'commission')
                            ->where('eshop_categroy_wise_commission.order_id',$items->order_id)
                            ->where('eshop_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                            @endphp                                        
                            <tr>
                                <td></td>
                                <td colspan="4">{{ $items->catname }} @if(sizeof($resultComm)>0) <span class="pull-right">Discount: {{$resultComm->commission}}, Value: {{$resultComm->order_commission_value}}</span> @endif</td>
                            </tr>

                            <?php
                            $itemsCount = 1;
                            $reultPro  = DB::table('eshop_order_details')
                            ->select( 'eshop_order_details.order_det_id','eshop_order_details.order_qty','eshop_order_details.order_total_value','eshop_order_details.deliverey_qty','eshop_order_details.delivery_value','eshop_order_details.product_id','eshop_order_details.p_unit_price', 'eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.party_id','eshop_product.id','eshop_product.name AS proname') 
                            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id') 
                            ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
                            ->where('eshop_order.order_status','Delivered') 
                            ->where('eshop_order.ack_status','Approved')                        
                            ->where('eshop_order_details.order_id',$invice->order_id)
                            ->where('eshop_product.category_id', $items->catid)    
                            ->get();
                            //dd($reultPro);
                            ?>
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
                                <td style="text-align: center;"> 
                                <input type="text" name="out_qty[{{$itemsPro->product_id}}]" value="{{ $itemsPro->order_qty }}" > 
                                </td>
                                <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td>

                                <input type="hidden" id="change_prod_price[]" value="{{ $itemsPro->p_unit_price }}" >

                                <input type="hidden" name="item_id[]" value="{{ $itemsPro->order_det_id }}" > 
                                <!-- <input type="hidden" name="out_qty1[{{$itemsPro->product_id}}]" value="{{ $itemsPro->order_qty }}" >  -->
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
                            <th style="text-align: center;">{{ number_format($totalQty,0) }}</th>
                            <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th> 
                        </tr>
                        <tr>
                            <th colspan="3" style="text-align: right;">Discount</th>
                            <th>&nbsp;</th>
                            <th style="text-align: center;"> 
                                {{ number_format($invice->total_discount,2) }} 
                            </th> 
                        </tr>
                        <tr>
                            <th colspan="3" style="text-align: right;">Net Amount</th>
                            <th>&nbsp;</th>
                            <th style="text-align: center;"> 
                                {{ number_format($totalPrice-$invice->total_discount,2) }}  
                            </th>
                           
                        </tr>

                    </tbody>

                </table>
                <input type="hidden" name="orderid" id="orderid" value="{{ $invice->order_id }}">
                <input type="hidden" name="retailderid" id="retailderid" value="{{ $invice->party_id }}">
                <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                @if($invice->stock_out==1)
                <div class="row" style="text-align: center;">
                    <div class="col-sm-3">
                    <button class="btn bg-pink btn-block btn-lg waves-effect">Make Invoice</button>                    
                    </div>
                </div> 
                @endif
            </div>
            @endif
        </div>
    </div>
    </form>
</div> 

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