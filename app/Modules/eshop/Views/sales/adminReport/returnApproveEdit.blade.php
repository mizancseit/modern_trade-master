@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Return
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
<form action="{{ URL('/not-return-remarks-submit') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)

                <div class="header">
                    <div class="row">
                        <div class="col-sm-8 col-md-8">
                            <th align="left" valign="top" style="font-weight: normal;vertical-align:top">
                                    PO No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->po_no }}<br />
                                    Customer Code &nbsp;:&nbsp; {{ $customerInfo->sap_code }}<br />
                                    
                                    Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}
                                    <br />
                                    
                                   Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                    @if($customerInfo->route_id==1)
                                    Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                    @endif
                                </th>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <th align="left" valign="top" style="font-weight: normal;vertical-align:top">                                       
                                    
                                     Return No : {{ $resultInvoice->return_no }}<br />
                                    Collected By &nbsp; : {{$resultFoInfo->display_name}}<br /> 
                                    Date &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->return_date }}

                                </th>
                        </div>

                        
                    </div>                                                           
                </div>

                <div class="header">
                    <h2>Order Return</h2>                            
                </div>

                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="headerTable" width="3%" valign="middle">Sl.</th>
                                <th class="headerTable" width="20%">Products Group</th>
                                <th class="headerTable" width="10%" valign="middle">Products Code</th>
                                <th class="headerTable" width="30%" valign="middle">Item</th>
                                
                                {{-- <th class="headerTable" width="7%" valign="middle">Unit Price</th> --}}
                                <th class="headerTable" width="10%" valign="middle">Order Qty</th>      
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
                            $totalRepDelv = 0;
                            $totalPrice = 0;
                            $totalFreeQty = 0;
                            $totalDeliveryQty = 0;
                            $totalDeliveryPrice = 0;
                            $totalPerUnitPrice = 0;
                            @endphp
                            @foreach($resultCartPro as $items)
                                                                  
                            <tr>
                                <td></td>
                                <td colspan="4">{{ $items->catname }} </td>
                            </tr>

                            @php 
                            $itemsCount = 1;
                           

                            $reultPro  = DB::table('eshop_return_details')
                                    ->select('eshop_return_details.p_unit_price','eshop_return_details.deliverey_qty','eshop_return_details.return_det_id','eshop_return_details.cat_id','eshop_return_details.return_id','eshop_return_details.order_qty','eshop_return_details.order_total_value','eshop_return_details.product_id','eshop_return_details.p_unit_price','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_return.return_id','eshop_return.fo_id','eshop_return.order_status','eshop_return.party_id','eshop_product.id','eshop_product.name AS proname','eshop_product.sap_code')
                                    ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_return_details.cat_id')
                                    ->join('eshop_return', 'eshop_return.return_id', '=', 'eshop_return_details.return_id')
                                    ->join('eshop_product', 'eshop_product.id', '=', 'eshop_return_details.product_id')
                                    //->where('eshop_return.order_status','Delivered')
                                    ->where('eshop_return_details.return_id',$orderMainId)
                                    ->where('eshop_return_details.cat_id', $items->catid)    
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

                            </tr>
                            @php
                            $serial ++;
                            @endphp
                            @endforeach                                            
                        </tr>

                        @endforeach
                        @endif 
                       <tr>
                                <th colspan="4"  class="rowTableLeft">Total Amount</th>
                                <th class="rowTableRight">{{number_format($totalQty,0)}}</th>
                               {{--  <th class="rowTableRight">{{number_format($totalPrice,2)}}</th>                                 
                                <th class="rowTableRight">
                                    {{number_format($totalDeliveryQty,0)}}
                                </th>
                                <th class="rowTableRight">{{number_format($totalDeliveryPrice,2)}}</th>  --}}                                           
                            </tr> 
                        <tr>
                            <td colspan="1" style="text-align: right;">Remarks</td>
                            
                            <td  colspan="4" style="text-align: center;"><textarea class="form-control" rows="2" name="remarks" id="remarks"></textarea></td> 
                        </tr>

                    </tbody>

                </table>
                <p></p>
                <div class="row" style="text-align: center;">
                    <div class="col-sm-3">
                        <a href="{{ URL('/eshop-return-delivery-approved-submit/'.$resultInvoice->return_id.'/'.$resultInvoice->party_id.'/'.'yes') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Approved</button>
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        {{-- <a href="{{ URL('/eshop-return-delivery-approved-submit/'.$resultInvoice->return_id.'/'.$resultInvoice->party_id.'/'.'no') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        </a> --}}
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->return_id }}">
                <input type="hidden" name="party_id" id="party_id" value="{{ $resultInvoice->party_id }}">
                <input type="hidden" name="customer_id" id="customer_id" value="{{ $customerInfo->customer_id }}">
                <input type="hidden" name="foMainId" id="foMainId" value="{{ $foMainId }}">
                <input type="hidden" name="remarks_type" id="remarks_type" value="return">

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