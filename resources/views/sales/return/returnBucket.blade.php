@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        BUCKET
                        <small> 
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/returnproduct') }}"> Return </a> / <a href="{!! URL::previous() !!}"> New Order </a> / Bucket
                       </small>
                   </h2>
               </div>

               <div class="col-lg-3">
                <a href="{{ URL('/return-process/'.$retailderid.'/'.$routeid) }}">
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
            <form id="myForm" action="{{ URL('/confirm-return') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->
            <div class="body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product Group</th>
                            <th>Product Name</th>
                            <th>Return Qty</th>                                            
                            <th>Return Value</th>
                            <th>Change Category</th>
                            <th>Change Name</th>
                            <th>Change Qty</th> 
                            <th>Change Value</th>
                            <th>Action</th>                                            
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <th colspan="9">{{ $resultRetailer->name }}</th>                  
                        </tr>

                        @if(sizeof($resultCartPro)>0)
                        @php
                        $serial   = 1;
                        $count    = 1;
                        $totalReturnQty = 0;
                        $totalReturnValue = 0;
                        $totalChangeQty = 0;
                        $totalChangeValue = 0;
                        @endphp
                        
                        @foreach($resultCartPro as $itemsPro)                                       
                        
                    
                        <tr>
                            
                            <td>{{ $serial }}</td>
                            
                            <td>{{ $itemsPro->ret_catname }}</td>
                            
                            <td>@if($itemsPro->return_qty!='') {{ $itemsPro->ret_cproname }} @endif</td>
                            
                            <td style="text-align: right;">
                                @if($itemsPro->return_qty!='')
                                    @if($itemsPro->return_qty=='') {{ 0 }} 
                                        @else {{ $itemsPro->return_qty }} 
                                    @endif
                                @endif
                            </td>
                            <td>@if($itemsPro->return_qty!='') {{ $itemsPro->return_value }} @endif</td>
                            <td>{{ $itemsPro->chan_catname }}</td>
                            <td>{{ $itemsPro->chng_proname }}</td>
                            
                            <td style="text-align: right;">@if($itemsPro->change_qty=='') {{ 0 }} @else {{ $itemsPro->change_qty }} @endif</td>
                            
                            <td>{{ $itemsPro->change_value }}</td>
                            
                            <td>
                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editReturnProducts('{{ $itemsPro->return_order_det_id }}')" style="width: 70px;">

                                @if($itemsPro->return_qty==0)
                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="returnItemDelete('{{ $itemsPro->return_order_det_id }}')" style="width: 70px; margin-top: 0px;">
                                @endif

                                <input type="hidden" id="order_det_id{{ $serial }}" value="{{ $itemsPro->return_order_det_id }}">
                                <input type="hidden" id="pro_id{{ $serial }}" value="{{ $itemsPro->return_product_id }}">
                                <input type="hidden" id="order_id{{ $serial }}" value="{{ $itemsPro->return_order_id }}">
                                <input type="hidden" id="return_qty{{ $serial }}" value="{{ $itemsPro->return_qty }}">
                                <input type="hidden" id="p_unit_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}">
                                <input type="hidden" id="cat_id{{ $serial }}" value="{{ $itemsPro->return_cat_id }}">

                            </td>
                            
                        </tr>
                        
                        @php
                            $serial ++;
                            $totalReturnQty += $itemsPro->return_qty;
                            $totalReturnValue += $itemsPro->return_value;
                            $totalChangeQty += $itemsPro->change_qty;
                            $totalChangeValue += $itemsPro->change_value;
                        @endphp
                        
                            @endforeach
                        @endif
                        
                        <input type="hidden" id="itemsid" value="">
                        <tr>
                            <th colspan="3" style="text-align: center;">Total</th>
                            <th style="text-align: right;">{{$resultInvoice->total_return_qty}}</th>
                            <th style="text-align: right;">{{$resultInvoice->total_return_value}}</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                             <th style="text-align: right;">{{$totalChangeQty}}</th>
                             <th style="text-align: right;">{{$totalChangeValue}}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tbody>
                </table>

                <input type="hidden" name="returnValue" id="returnValue" value="{{ $totalReturnValue }}">
                <input type="hidden" name="changeValue" id="changeValue" value="{{ $totalChangeValue }}">
                <input type="hidden" name="changeQty" id="changeQty" value="{{ $totalChangeQty }}">

                <input type="hidden" name="orderid" value="{{ $resultInvoice->return_order_id }}">
                <input type="hidden" name="retailderid" value="{{ $retailderid }}">
                <input type="hidden" name="routeid" value="{{ $routeid }}">
                <input type="hidden" name="pointid" value="{{ $pointid }}">
                <input type="hidden" name="distributorID" value="{{ $distributorID }}">
                
                <p></p>
                <div class="row" style="text-align: center;">


                    <div class="col-sm-3">
                        <a href="JavaScript:void(0)" onclick="checkReturnValueAndChangeValueConfirm()">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Return Request</button>
                        </a>
                    </div>
                    <div class="col-sm-2">
                        <a href="{{ URL('/delete-return/'.$retailderid.'/'.$routeid.'/'.$resultInvoice->return_order_id) }}" onclick="return confirm('Are you sure you want to delete this return change?');">
                            <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-return-delete">Delete</button>
                        </a>
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->return_order_id }}">
                <input type="hidden" name="retailderid" id="retailderid" value="{{ $retailderid }}">
                <input type="hidden" name="routeid" id="routeid" value="{{ $routeid }}">
                <input type="hidden" name="type" id="type" value="1">
            </div>
            </form>
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
                                <a href="{{ URL('/return-process/'.$retailderid.'/'.$routeid) }}">
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