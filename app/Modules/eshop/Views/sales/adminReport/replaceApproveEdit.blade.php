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
<form action="{{ URL('/not-approved-remarks-submit') }}" method="POST">
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
                            <strong>

                                Collected By &nbsp; : {{$resultInvoice->display_name}}<br />
                                Invoice No : {{ $resultInvoice->replace_no }}<br />
                                Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->replace_date)) }}

                            </strong>
                        </div>
                    </div>                                                           
                </div>

                <div class="header">
                    <h2>Advance Replace</h2>                            
                </div>

                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Req P.Name</th>
                                <th>Req P.Qty</th>
                               {{--  <th>Req P.Value</th>  --}} 
                                <th>Product Name</th>
                                <th>Replace Qty</th>
                               {{--  <th>Replace Value</th>           --}}       
                            </tr>
                        </thead>

                        <tbody>

                            @if(sizeof($resultCartPro)>0)
                            @php
                            $serial   = 1;
                            $count    = 1;
                            $deliveryQty = 0;
                            $deliveryValue = 0;
                            $totalQty = 0;
                            $totalPrice = 0;
                            @endphp
                            @foreach($resultCartPro as $items)
                                                                  
                            <tr>
                                <td></td>
                                <td colspan="3">{{ $items->catname }} </td>
                            </tr>

                            @php 
                            $itemsCount = 1;
                            $reultPro  = DB::table('eshop_replace_details')
                            ->select('eshop_replace_details.replace_det_id','eshop_replace_details.cat_id','eshop_replace_details.replace_id','eshop_replace_details.order_qty','eshop_replace_details.order_total_value','eshop_replace_details.deliverey_qty','eshop_replace_details.delivery_value', 
                                'eshop_replace_details.product_id','eshop_replace_details.replace_product_id','eshop_replace_details.order_qty','eshop_replace_details.p_unit_price','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_replace.replace_id','eshop_replace.fo_id','eshop_replace.order_status','eshop_replace.party_id','eshop_product.id','eshop_product.name AS proname')
                            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_replace_details.cat_id')
                            ->join('eshop_replace', 'eshop_replace.replace_id', '=', 'eshop_replace_details.replace_id')
                            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_replace_details.product_id')

                            ->where('eshop_replace.order_status','Delivered')                        
                            ->where('eshop_replace.fo_id',$foMainId)                        
                            ->where('eshop_replace_details.replace_id',$DeliveryMainId)
                            ->where('eshop_replace_details.cat_id', $items->catid)    
                            ->get();
                            //dd($reultPro);
                            @endphp
                            @foreach ($reultPro as $itemsPro)
                            @php
                            $deliveryQty += $itemsPro->deliverey_qty;
                            $deliveryValue += $itemsPro->delivery_value;
                            $totalQty += $itemsPro->order_qty;
                            $totalPrice += $itemsPro->order_total_value;


                            $replace_product = DB::table('eshop_product')
                                    ->where('id', $itemsPro->replace_product_id)    
                                    ->first();

                            @endphp

                            <tr>
                                <td>{{ $serial }}</td>
                                <td></td>
                                <td>{{ $itemsPro->proname }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->order_qty,2) }}</td>
                                {{-- <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td> --}}

                                <td>
                                    @if(sizeof($replace_product)>0){{ $replace_product->name }} @endif

                                </td>
                                <td style="text-align: center;">{{ number_format($itemsPro->deliverey_qty,2) }}</td>
                               {{--  <td style="text-align: center;">{{ number_format($itemsPro->delivery_value,2) }}</td> --}}

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
                            {{-- <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th> --}}
                            <th></th>
                            <th style="text-align: center;">{{ number_format($deliveryQty,2) }}</th>
                           {{--  <th style="text-align: center;">{{ number_format($deliveryValue,2) }}</th> --}}

                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;">Remarks</td>
                            
                            <td  colspan="4" style="text-align: center;"><textarea class="form-control" rows="2" name="remarks" id="remarks"></textarea></td>
                            <td></td>
                        </tr>

                    </tbody>

                </table>
                <p></p>
                <div class="row" style="text-align: center;">
                    <div class="col-sm-3">
                        <a href="{{ URL('/eshop-replace-delivery-approved-submit/'.$resultInvoice->replace_id.'/'.$resultInvoice->party_id.'/'.'yes') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Approved</button>
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        {{-- <a href="{{ URL('/eshop-replace-delivery-approved-submit/'.$resultInvoice->replace_id.'/'.$resultInvoice->party_id.'/'.'no') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        </a> --}}
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->replace_id }}">
                <input type="hidden" name="party_id" id="party_id" value="{{ $resultInvoice->party_id }}">
                <input type="hidden" name="customer_id" id="customer_id" value="{{ $customerInfo->customer_id }}">
                <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                <input type="hidden" name="remarks_type" id="remarks_type" value="replace">

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