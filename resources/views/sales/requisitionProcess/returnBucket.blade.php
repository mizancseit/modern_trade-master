@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        RETURN BUCKET
                        <small> 
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/fo/return-only-product') }}"> Return </a> / <a href="{!! URL::previous() !!}"> New Return Order </a> / Return Bucket
                       </small>
                   </h2>
               </div>

               <div class="col-lg-3">
                <a href="{{ URL('/fo/return-only-process/'.$retailderid.'/'.$routeid) }}">
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

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">                        

            @if(sizeof($resultCartPro)>0)
            <div class="header">
                <h2>ALL RETURN BUCKET PRODUCT</h2>                            
            </div>
            <div class="body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product Group</th>
                            <th>Product Name</th>
                            <th>Return Qty</th>                                            
                    		
							<th>Action</th>                                            
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <th colspan="8">{{ $resultRetailer->name }}</th>                  
                        </tr>

                        @if(sizeof($resultCartPro)>0)
                        @php
                        $serial   = 1;
                        $count    = 1;
                        $subTotal = 0;
                        $totalReturn = 0;
                        @endphp
                        
						@foreach($resultCartPro as $itemsPro)
                        @php
                            $totalReturn += $itemsPro->return_only_qty;
                        @endphp                                    
                        
					
                        <tr>
                            
							<td>{{ $serial }}</td>
                            
							<td>{{ $itemsPro->ret_catname }}</td>
                            
							<td>{{ $itemsPro->ret_cproname }}</td>
                            
                            <td style="text-align: right;">@if($itemsPro->return_only_qty=='') {{ 0 }} @else {{ $itemsPro->return_only_qty }} @endif</td>

                         
							
							
							<td>
                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editReturnOnlyProductsM('{{ $itemsPro->return_only_order_det_id }}')" style="width: 70px;">

                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="returnOnlyItemDeleteM('{{ $itemsPro->return_only_order_det_id }}')" style="width: 70px; margin-top: 0px;">

                                <input type="hidden" id="order_det_id{{ $serial }}" value="{{ $itemsPro->return_only_order_det_id }}">
                                <input type="hidden" id="pro_id{{ $serial }}" value="{{ $itemsPro->return_only_product_id }}">
                                <input type="hidden" id="order_id{{ $serial }}" value="{{ $itemsPro->return_only_order_id }}">
                                <input type="hidden" id="return_qty{{ $serial }}" value="{{ $itemsPro->return_only_qty }}">
                                <input type="hidden" id="p_unit_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}">
                                <input type="hidden" id="cat_id{{ $serial }}" value="{{ $itemsPro->return_only_cat_id }}">

                            </td>
							
                        </tr>
						
                        @php
							$serial ++;
                        @endphp
                        
							@endforeach
                        @endif
                        
						<input type="hidden" id="itemsid" value="">
                        <tr>
                            <th colspan="3" style="text-align: center;">Total</th>
                            
                            <th style="text-align: right;">{{$totalReturn}}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tbody>
                </table>
				
                <p></p>
                <div class="row" style="text-align: center;">


                    <div class="col-sm-3">
                        <a href="{{ URL('/fo/confirm-only-return/'.$resultInvoice->return_only_order_id.'/'.$resultInvoice->return_only_order_no.'/'.$retailderid.'/'.$routeid.'/'.$pointid.'/'.$distributorID) }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Return Request</button>
                        </a>
                    </div>
                    <div class="col-sm-2">
                            <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-return-delete-distributor">Delete</button>
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->return_only_order_id }}">
                <input type="hidden" name="retailderid" id="retailderid" value="{{ $retailderid }}">
                <input type="hidden" name="routeid" id="routeid" value="{{ $routeid }}">
                <input type="hidden" name="type" id="type" value="1">
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
                                <a href="{{ URL('/return-only-process/'.$retailderid.'/'.$routeid) }}">
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
<!-- #END# Basic Validation -->            
</div>
</section>
@endsection