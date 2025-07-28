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
                           <a href="{{ URL('/dashboard') }}">Dashboard</a>/<a href="{{ URL('/mts-visit') }}"> Visit </a>/<a href="{!! URL::previous() !!}">Order</a>/Bucket
                        </small>
                   </h2>
               </div>

               <div class="col-lg-3">
                <a href="{{ URL('/mts-order-process/'.$partyid.'/'.$customer_id.'?order_id='.$order_id) }}">
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
<form action="{{ URL('/mts-confirm-order') }}" method="post">
      {{ csrf_field() }}  
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)

                <table class="table table-bordered invoiceHeader" style="border:1px solid #E91E63;">
                    <tdead>
                        <tr>
                         <th width="50%" align="left" valign="top" style="font-weight: normal;vertical-align:top; color: #000;">
                            Order No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultFoInfo->order_no }}<br />
                            Customer Name &nbsp;&nbsp; :&nbsp; {{ $resultFoInfo->cname }}<br />
                            Customer Code &nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{$resultFoInfo->sap_code}}<br />
                            Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultFoInfo->partyName }}<br />
                            Outlet Address &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultFoInfo->address }}<br />
                         </th>
                         <th width="45%" align="left" valign="top" style="font-weight: normal;vertical-align:top; color: #000;"> 
                            Collected By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {{ $resultFoInfo->display_name }}<br />
                            Collected Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultFoInfo->order_date }}<br />
                         </th>
                        </tr>
                    </tdead>
                </table>
                <div class="header">
                      
                    <div class="row">
                         <div class="col-lg-3">
                            <h2>Order Products</h2>  
                         </div>
                         <div class="col-lg-2 text-right" style="padding-top: 8px;"><h2>Credit Limit.</h2> </div>

                         @if($creditSummery>0)
                         <div class="col-lg-2 text-left">
                         <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-success" value="{{ $creditSummery}}" readonly="">  </div>
                         @else
                         <div class="col-lg-2 text-left">
                         <input type="text" name="credit_limit" id="credit_limit" class="form-control btn-danger" value="{{ $creditSummery}}" readonly="">  </div>
                         @endif

                          <div class="col-lg-2 text-right" style="padding-top: 8px;"><h2>PO NO.</h2> </div>
                        <div class="col-lg-3 text-left">
                         <input type="text" name="po_no" id="po_no" class="form-control" required="" value="{{ $resultInvoice->po_no}}"> 
                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id}}">
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
                                <th>Order Qty</th>
                                <th>Unit price</th>
                                <th>Discount %</th>
                                <th>Order Value</th>                               
                                <th>Cat. Dis. %</th>                               
                                <th>Net Amount</th>                               
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
                                @php
                                $resultComm = DB::table('mts_categroy_wise_commission')
                                ->select('order_commission_value', 'commission')
                                ->where('mts_categroy_wise_commission.order_id',$items->order_id)
                                ->where('mts_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                                @endphp   

                                                              
                            <tr>
                                <th></th>
                                <th colspan="11">{{ $items->catname }}  @if(sizeof($resultComm)>0) <span class="pull-right">Discount: {{$resultComm->commission}} %, Value: {{$resultComm->order_commission_value}}</span>@endif</th> 
                            </tr>
                            

                            @php 
                            $reultPro  = DB::table('mts_order_details')
                            ->select('mts_order_details.order_det_id', 'mts_order_details.discount','mts_order_details.product_id','mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid',
                            'tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status',
                            'mts_order.order_no','mts_order.party_id','mts_order_details.order_qty','mts_order_details.p_unit_price','mts_order_details.order_total_value','mts_order_details.order_date','mts_order_details.discount_rate','tbl_product.name as proname','tbl_product.sap_code')

                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id') 
                            ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
                            // ->where('mts_order.order_status','Ordered')                        
                            ->where('mts_order.fo_id',Auth::user()->id)                        
                            ->where('mts_order.party_id',$partyid)
                            ->where('mts_order.order_id',$resultInvoice->order_id)
                            ->where('mts_order_details.cat_id', $items->catid)
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
                                
                                <td style="text-align: right;">@if($itemsPro->order_qty=='') {{ 0 }} @else {{ $itemsPro->order_qty }} @endif</td> 
                                <td style="text-align: right;">@if($itemsPro->p_unit_price=='') {{ 0 }} @else {{ $itemsPro->p_unit_price }} @endif</td>
                                <td style="text-align: right;">@if($itemsPro->discount=='') {{ 0 }} @else {{ $itemsPro->discount }} @endif</td>
                                
                                
                                <td style="text-align: right;">@if($itemsPro->order_total_value=='') {{ 0 }} @else {{ $itemsPro->order_total_value }} @endif</td>
                                <td style="text-align: right;"> @if(sizeof($resultComm)>0) 
                                    <span class="pull-right"> {{$resultComm->commission}} %</span>@endif</td>
                                
                                <td style="text-align: right;">@if($itemsPro->order_total_value=='') {{ 0 }} @else 
                                    <?php 
                                        $selling_price = ($itemsPro->order_total_value * ($itemsPro->discount_rate / 100));
                                    ?>
                                    {{ $itemsPro->order_total_value - $selling_price }} @endif </td>
                                <td>
                                    <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editProducts('{{ $itemsPro->order_id }}','{{ $itemsPro->order_det_id }}','{{ $customer_id }}','{{ $partyid }}','{{ $items->catid }}','{{ $itemsPro->product_id }}')" style="width: 70px;">
                                    <a href="{{ URL('/mts-items-delete/'.$itemsPro->order_id.'/'.$itemsPro->order_det_id.'/'.$customer_id.'/'.$partyid.'/'.$items->catid) }}">
                                        <input type="button" class="btn bg-red btn-block btn-sm waves-effect" value="Delete" style="width: 70px;">
                                    </a>

                                    {{-- <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="itemDelete('{{ $itemsPro->order_id }}','{{ $itemsPro->order_det_id }}','{{ $customer_id }}','{{ $partyid }}','{{ $items->catid }}','{{ $itemsPro->product_id }}')" style="width: 70px; margin-top: 0px;"> --}}

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
                                <th style="text-align: center;"></th>
                                <th style="text-align: center;"></th>
                                <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                <th>&nbsp;</th>
                            </tr>

                            <tr>
                                <th colspan="4" style="text-align: center;">Total Discount</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                
                                <th style="text-align: right;">@if(sizeof($orderCommission)>0)
                                            {{ number_format($orderCommission->commission,2) }} @else {{'0.00'}}  @endif</th> 
                                <th>&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="4" style="text-align: center;">Net Total</th>
                                <th style="text-align: right;">{{number_format($totalQty,0)}}</th>
                                <th></th>
                                <th></th>
                                <th style="text-align: right;">{{number_format($totalValue-$orderCommission->commission,2)}}</th>  
                                <th>&nbsp;</th>  
                            </tr>
                            @php
                                $remarksResult = DB::table('mts_remarks')
                                    ->where('reference_id',$resultInvoice->order_id)
                                    ->where('customar_id',$customer_id)
                                    ->where('party_id',$partyid)
                                    ->where('remarks_type','order') 
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
                            <a href="{{ URL('/mts-delete-order/'.$resultInvoice->order_id.'/'.$partyid.'/'.$customer_id) }}">
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
