@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            DELIVERY ENTRY
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Wastage
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
            
            <form action="{{ URL('/wastage-edit-submit') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">                        

                            @if(sizeof($resultCartPro)>0)
                            
                            <div class="header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <strong>To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                        Mobile &nbsp; : {{ $resultInvoice->mobile }}</strong>
                                    </div>

                                    <div class="col-sm-4">
                                        <strong>Invoice No : {{ $resultInvoice->order_no }}<br />
                                        Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->order_date)) }}</strong>
                                    </div>
                                </div>                                                           
                            </div>

                            <div class="header">
                                <h2>DELIVERY ENTRY</h2>                            
                            </div>

                            <div class="body">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product Group</th>
                                                <th>Product Name</th>
                                                <th>Wastage</th>
                                                <th>Value</th>
                                                <th>Replace Delivery</th>
                                                <th>Delivery Value</th>                   
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
                                                <th></th>
                                                <th colspan="9">{{ $items->catname }}</th>
                                            </tr>

                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('tbl_wastage_details')
                                                    ->select('tbl_wastage_details.replace_delivered_qty','tbl_wastage_details.order_det_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_total_price','tbl_wastage_details.product_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                                                    ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')

                                                    ->where('tbl_wastage.order_type','Confirmed')                        
                                                    ->where('tbl_wastage.fo_id',$foMainId)                        
                                                    ->where('tbl_wastage_details.order_id',$wastageMainId)
                                                    ->where('tbl_wastage_details.cat_id', $items->catid)    
                                                    ->get();
                                                   //dd($reultPro);
                                                @endphp
                                                @foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalQty += $itemsPro->wastage_qty;
                                                $totalPrice += $itemsPro->wastage_qty * $itemsPro->p_unit_price;

                                                @endphp

                                                <tr>
                                                <th>{{ $serial }}</th>
                                                <th></th>
                                                <th>{{ $itemsPro->proname }}</th>
                                                <th style="text-align: center;">{{ $itemsPro->wastage_qty }}</th>
                                                <th style="text-align: center;">{{ number_format($itemsPro->wastage_qty * $itemsPro->p_unit_price,0) }}</th>
                                                
                                                <th style="text-align: center;">
                                                    <input type="number" name="qty[]" id="qty{{ $serial }}" value="{{ $itemsPro->wastage_qty }}" class="form-control" style="width: 70px; text-align: center;" onkeyup="wastageQty({{ $serial }})" pattern="[1-9]" min="1" maxlength="8">
                                                </th>
                                                <th style="text-align: center;" id="rowPrice{{ $serial }}">{{ number_format($itemsPro->wastage_qty * $itemsPro->p_unit_price,0) }}</th>
                                                <input type="hidden" name="oldqty[]" id="oldqty{{ $serial }}" value="{{ $itemsPro->wastage_qty }}">
                                                <input type="hidden" name="price[]" id="price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}" >
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
                                                <th colspan="3" align="right">Sub Total</th>
                                                <th style="text-align: center;">{{ $totalQty }}</th>
                                                <th style="text-align: center;">{{ number_format($totalPrice,0) }}</th>
                                               
                                                <th style="text-align: center;" id="totalQty">{{ $totalQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <th style="text-align: center;" id="totalPrice">{{ number_format($totalPrice,0) }}
                                                    <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                                                </th>
                                                                                      
                                            </tr>
                                            
                                        </tbody>
                                       
                                    </table>
                                    <p></p>
                                    <div class="row" style="text-align: center;">
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->order_id }}">
                                    <input type="hidden" name="retailderid" id="retailderid" value="{{ $resultInvoice->retailer_id }}">
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