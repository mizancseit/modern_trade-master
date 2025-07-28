@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        BUCKET
                        <small> 
                           <a href="{{ URL('/dashboard') }}">Dashboard</a>/<a href="{{ URL('/mts-replace') }}"> Replace </a>/<a href="{!! URL::previous() !!}">Order</a>/Bucket
                       </small>
                   </h2>
               </div>

               <div class="col-lg-3">
                <a href="{{ URL('/mts-replace-process/'.$partyid.'/'.$customer_id) }}">
                    <button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                </a>                        
            </div>

        </div>
    </div>
</div>

@if(Session::has('success'))
<div class="alert alert-success">
    {{ Session::get('success') }}                        
</div>
@endif
<form action="{{ URL('/mts-confirm-replace') }}" method="post">
      {{ csrf_field() }}  
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)
                <div class="header">
                    <div class="row">
                         <div class="col-lg-7">
                            <h2>Replace Products</h2>  
                         </div>

                          <div class="col-lg-2 text-right" style="padding-top: 8px;"><h2>PO NO.</h2> </div>
                        <div class="col-lg-3 text-left">
                         <input type="text" name="po_no" id="po_no" class="form-control" value="" required=""> 
                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->replace_id}}">
                    <input type="hidden" name="partyid" id="partyid" value="{{ $partyid }}">
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer_id }}">
                        </div>
                    </div>
                                               
                </div>
                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                {{-- <th>Unit price</th> --}}
                                <th>Replace Qty</th>
                                {{-- <th>Order Value</th>  --}}                              
                                <th>Action</th>                                           
                            </tr>
                        </thead>

                        <tbody>

                             @if(sizeof($resultCartPro)>0)
                                @php
                                $serial   = 1;
                                $count    = 1;
                                $subTotal = 0;
                                $totalQty = 0;
                                $totalValue = 0;
                                
                                @endphp
                                @foreach($resultCartPro as $items)  
                                                                     
                            <tr>
                                <th></th>
                                <th colspan="5">{{ $items->catname }}</th> 
                            </tr>


                            @php 
                            $reultPro  = DB::table('mts_replace_details')
                            ->select('mts_replace_details.replace_det_id','mts_replace_details.product_id','mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid',
                            'tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status',
                            'mts_replace.replace_no','mts_replace.party_id','mts_replace_details.order_qty','mts_replace_details.p_unit_price','mts_replace_details.order_total_value','mts_replace_details.replace_date','tbl_product.name as proname','tbl_product.sap_code')

                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id') 
                            ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'mts_replace_details.product_id')
                            ->where('mts_replace.order_status','Ordered')                        
                            ->where('mts_replace.fo_id',Auth::user()->id)                        
                            ->where('mts_replace.party_id',$partyid)
                            ->where('mts_replace_details.cat_id', $items->catid)
                            ->get();
                               @endphp
                                @foreach ($reultPro as $itemsPro)
                                @php
                                $totalValue += $itemsPro->order_total_value;
                                $totalQty += $itemsPro->order_qty; 
                                @endphp

                            <tr>
                                <td>{{ $serial }}</td>
                                <td></td>
                                <td>{{ $itemsPro->sap_code }}</td>
                                <td>{{ $itemsPro->proname }}</td>
                                {{-- <td style="text-align: right;">@if($itemsPro->p_unit_price=='') {{ 0 }} @else {{ $itemsPro->p_unit_price }} @endif</td> --}}
                                
                                <td style="text-align: right;">@if($itemsPro->order_qty=='') {{ 0 }} @else {{ $itemsPro->order_qty }} @endif</td> 
                                
                                
                                {{-- <td style="text-align: right;">@if($itemsPro->order_total_value=='') {{ 0 }} @else {{ $itemsPro->order_total_value }} @endif</td> --}}
                                <td>
                                    <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="replaceItemEdit('{{ $itemsPro->replace_det_id }}')" style="width: 70px;">

                                    <a href="{{ URL('/mts-replace-items-delete/'.$itemsPro->replace_id.'/'.$itemsPro->replace_det_id)}}">
                                        <input type="button" class="btn bg-red btn-block btn-sm waves-effect" value="Delete" style="width: 70px;">
                                    </a>

                                </td>
                            </tr>
                            @php
                            $serial ++;
                            @endphp
                            @endforeach   

                            @endforeach
                            @endif 
                            <tr>
                                <th colspan="4" style="text-align: center;">Total</th>
                                 
                                <th style="text-align: right;">{{ number_format($totalQty,0) }}</th>
                                {{-- <th style="text-align: right;">{{ number_format($totalValue,2) }}</th> --}}
                                <th>&nbsp;</th>
                            </tr> 

                            @php
                                $remarksResult = DB::table('mts_remarks')
                                    ->where('reference_id',$resultInvoice->replace_id)
                                    ->where('customar_id',$customer_id)
                                    ->where('party_id',$partyid)
                                    ->where('remarks_type','replace') 
                                    ->first();

                                @endphp

                                @if(sizeof($remarksResult)>0)
                                 <tr>
                                    <td colspan="2" style="text-align: center;">Remarks</td>
                                      
                                    <td colspan="6">{{$remarksResult->remarks}}</td>  
                                </tr> 
                                @endif

                        </tbody>
                    </table>
                    <p></p>
                    <div class="row" style="text-align: center;">


                        <div class="col-sm-3">

                             <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Order</button>
                            {{-- <a href="{{ URL('/mts-confirm-order/'.$resultInvoice->order_id.'/'.$partyid.'/'.$routeid) }}">
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Order</button>
                            </a> --}}
                        </div>
                        <div class="col-sm-2">
                            <a href="{{ URL('/mts-delete-replace/'.$resultInvoice->replace_id.'/'.$partyid.'/'.$customer_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-lg waves-effect">Delete</button>
                            </a>
                        </div>
                    </div>
                </div>
                @else

                <div class="header">
                    <h2>BUCKET EMPTY</h2>                            
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="7"></th>                                                         
                        </tr>
                    </thead>
                    <tbody>                                        
                        <tr>
                            <th colspan="7" style="color: #000; text-align: center;" align="center">
                                <h4>YOUR BUCKET PRODUCT IS EMPTY.</h4> <p></p><p></p>

                                <div class="col-sm-4" style="margin-right: 40px;"></div>
                                <div class="col-sm-3">
                                    <a href="{{ URL('/mts-order-process/'.$partyid.'/'.$customer_id) }}">
                                        <button type="button" class="btn bg-red btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                                    </a>
                                </div> 

                            </th>                  
                        </tr>
                        <tr>
                            <th colspan="7"></th>                                                         
                        </tr>
                    </tbody>
                </table>

                @endif
            </div>
        </div>
    </div>
</form>
<!-- #END# Basic Validation -->            
</div>
</section>
@endsection