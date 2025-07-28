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
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/wastage') }}"> Wastage </a> / <a href="{!! URL::previous() !!}"> New Order </a> / Bucket
                       </small>
                   </h2>
               </div>

               <div class="col-lg-3">
                <a href="{{ URL('/wastage-process/'.$retailderid.'/'.$routeid) }}">
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
                <h2>ALL WASTAGE BUCKET PRODUCT</h2>                            
            </div>
            <div class="body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product Group</th>
                            <th>Product Name</th>
                            <th>Wastage Qty</th>                                            
                            <th>Edit</th>                                            
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
                        $totalWastage = 0;
                        @endphp
                        @foreach($resultCartPro as $items)                                       
                        <tr>
                            <th></th>
                            <th colspan="9">{{ $items->catname }}</th>
                        </tr>


                        @php 
                        $itemsCount = 1;
                        $reultPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.order_det_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_wastage_details.p_total_price','tbl_wastage_details.product_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.retailer_id','tbl_product.id','tbl_product.name AS proname')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')

                        ->where('tbl_wastage.order_type','Ordered')                        
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage.retailer_id',$retailderid)
                        ->where('tbl_wastage_details.cat_id', $items->catid)    
                        ->get();
                        //dd($reultPro);
                        @endphp
                        @foreach ($reultPro as $itemsPro)
                        @php
                       
                        $totalWastage += $itemsPro->wastage_qty;

                        @endphp

                        <tr>
                            <td>{{ $serial }}</td>
                            <td></td>
                            <td>{{ $itemsPro->proname }}</td>
                            
                            <td style="text-align: right;">@if($itemsPro->wastage_qty=='') {{ 0 }} @else {{ $itemsPro->wastage_qty }} @endif</td>

                            <td>
                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editWastageProducts('{{ $itemsPro->order_det_id }}','{{ $pointid }}','{{ $routeid }}','{{ $retailderid }}','{{ $items->catid }}')" style="width: 70px;">

                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="wastageItemDelete('{{ $serial }}')" style="width: 70px; margin-top: 0px;">

                                <input type="hidden" id="order_det_id{{ $serial }}" value="{{ $itemsPro->order_det_id }}">
                                <input type="hidden" id="pro_id{{ $serial }}" value="{{ $itemsPro->product_id }}">
                                <input type="hidden" id="order_id{{ $serial }}" value="{{ $itemsPro->order_id }}">
                                <input type="hidden" id="wastage_qty{{ $serial }}" value="{{ $itemsPro->wastage_qty }}">
                                <input type="hidden" id="p_unit_price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}">
                                <input type="hidden" id="cat_id{{ $serial }}" value="{{ $itemsPro->cat_id }}">

                            </td>
                        </tr>
                        @php
                        $serial ++;
                        @endphp
                        @endforeach   

                        @endforeach
                        @endif
                        <input type="hidden" id="itemsid" value="">
                        <tr>
                            <th colspan="3" style="text-align: center;">Total</th>
                            
                            <th style="text-align: right;">{{ $totalWastage }}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tbody>
                </table>
                <p></p>
                <div class="row" style="text-align: center;">


                    <div class="col-sm-3">
                        <a href="{{ URL('/confirm-order/'.$resultInvoice->order_id.'/'.$resultInvoice->order_no.'/'.$retailderid.'/'.$routeid.'/'.$pointid.'/'.$distributorID) }}">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Order Request</button>
                        </a>
                    </div>
                    <div class="col-sm-2">
                        {{-- <a href="{{ URL('/delete-order/'.$resultInvoice->order_id.'/'.$retailderid.'/'.$routeid) }}"> --}}
                            <button type="button" class="btn bg-red btn-block btn-lg waves-effect" data-toggle="modal" data-target="#confirm-delete">Delete</button>
                        {{-- </a> --}}
                    </div>
                </div>
                <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
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
                                <a href="{{ URL('/wastage-process/'.$retailderid.'/'.$routeid) }}">
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