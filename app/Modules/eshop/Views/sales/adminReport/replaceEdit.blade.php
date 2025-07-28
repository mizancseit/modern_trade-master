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

                               
                                Replace No : {{ $resultInvoice->replace_no }}<br />
                                Collected By &nbsp; : {{$resultInvoice->display_name}}<br />
                                Date &nbsp;&nbsp; : {{ $resultInvoice->replace_date }}

                            </span>
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
                                <th>Product Name</th>
                                <th>Replace Qty</th>
                               {{--  <th>Replace Value</th>       --}}            
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
                            @endphp
                            @foreach($resultCartPro as $items)
                                                                  
                            <tr>
                                <td></td>
                                <td colspan="6">{{ $items->catname }} </td>
                            </tr>

                            @php 
                            $itemsCount = 1;
                            $reultPro  = DB::table('eshop_replace_details')
                            ->select('eshop_replace_details.replace_det_id','eshop_replace_details.cat_id','eshop_replace_details.replace_id','eshop_replace_details.order_qty','eshop_replace_details.order_total_value','eshop_replace_details.product_id','eshop_replace_details.order_qty','eshop_replace_details.p_unit_price','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_replace.replace_id','eshop_replace.fo_id','eshop_replace.order_status','eshop_replace.party_id','eshop_product.id','eshop_product.name AS proname')
                            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_replace_details.cat_id')
                            ->join('eshop_replace', 'eshop_replace.replace_id', '=', 'eshop_replace_details.replace_id')
                            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_replace_details.product_id')

                            ->where('eshop_replace.order_status','Confirmed')                        
                            ->where('eshop_replace.fo_id',$foMainId)                        
                            ->where('eshop_replace_details.replace_id',$DeliveryMainId)
                            ->where('eshop_replace_details.cat_id', $items->catid)    
                            ->get();
                            //dd($reultPro);
                            @endphp
                            @foreach ($reultPro as $itemsPro)
                            @php
                            $subTotal += $itemsPro->order_total_value;
                            $totalQty += $itemsPro->order_qty;
                            $totalPrice += $itemsPro->order_total_value;

                            @endphp

                            <tr>
                                <td>{{ $serial }}</td>
                                <td></td>
                                <td>{{ $itemsPro->proname }}</td>
                                <td style="text-align: center;">{{ number_format($itemsPro->order_qty,0) }}</td>
                                {{-- <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td> --}}

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
                            <th style="text-align: center;">{{ number_format($totalQty,0) }}</th>
                            {{-- <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th> --}}
                        </tr>


                         @php
                        $remarksResult = DB::table('eshop_remarks')
                            ->where('reference_id',$resultInvoice->replace_id)
                            ->where('customar_id',$customerInfo->customer_id)
                            ->where('party_id',$resultInvoice->party_id)
                            ->where('remarks_type','replace') 
                            ->first();

                        @endphp

                        @if(sizeof($remarksResult)>0)
                         <tr>
                            <td colspan="1" style="text-align: center;">Remarks</td>
                            <td colspan="3">{{$remarksResult->remarks}}</td>  
                        </tr> 
                        @else

                        <tr>
                            <td colspan="1" style="text-align: center;">Remarks</td>
                            <td  colspan="3" style="text-align: center;"><textarea class="form-control" rows="2" name="remarks" id="remarks"></textarea></td>
                           
                        </tr>

                        @endif

                    </tbody>

                </table>
                <p></p>
                @if(Auth::user()->user_type_id!=1)
                <div class="row" style="text-align: center;">
                    <div class="col-sm-3">
                        <a href="{{ URL('/eshop-approved-replace/'.$resultInvoice->replace_id.'/'.$resultInvoice->party_id.'/'.'yes') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Approved</button>
                        </a>
                    </div>
                    <div class="col-sm-3">
                         <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        {{-- <a href="{{ URL('/eshop-approved-replace/'.$resultInvoice->replace_id.'/'.$resultInvoice->party_id.'/'.'no') }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Not Approved</button>
                        </a> --}}
                    </div>
                </div>
                @endif
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

@endsection