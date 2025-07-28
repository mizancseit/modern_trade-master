@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            CHANGE DELIVERY ENTRY
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Return Order
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
            
            <form action="{{ URL('/return-order-edit-submit') }}" method="POST">
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
                                        <strong>Invoice No : {{ $resultInvoice->return_order_no }}<br />
                                        Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($resultInvoice->return_order_date)) }}</strong>
                                    </div>
                                </div>                                                           
                            </div>

                            <div class="header">
                                <h2>CHANGE DELIVERY ENTRY</h2>                            
                            </div>

                            <div class="body">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Return Product Group</th>
                                                <th>Return Product Name</th>
                                                <th>Return Order</th>
                                                <th>Return Value</th>
                                                <th>Change Delivery</th>
                                                <th>Change Delivery Value</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            
                                            @if(sizeof($resultCartPro)>0)
                                            @php
										
										//dd($resultCartPro);
										
                                            $serial   = 1;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalQty = 0;
                                            $totalWastage = 0;
                                            $totalPrice = 0;
                                            $totalFreeQty =0;
                                            @endphp
											
											
											echo '<pre/>'; 
												print_r($resultCartPro); 
											exit;
											
                                            @foreach($resultCartPro as $items)       

											
											
                                            <tr>
                                                <th></th>
                                                <th colspan="9">{{ $items->catname }}</th>
                                            </tr>

                                                @php 
                                                
												$itemsCount = 1;
                                        
										$reultPro  = DB::table('tbl_return_details')
                                                    ->select('tbl_return_details.change_qty','tbl_return_details.return_order_det_id','tbl_return_details.return_cat_id',
													'tbl_return_details.return_order_id','tbl_return_details.return_qty','tbl_return_details.p_total_price',
													'tbl_return_details.return_product_id','tbl_return_details.p_unit_price', 'tbl_product_category.id AS catid',
													'tbl_product_category.name AS catname','tbl_return.return_order_id','tbl_return.fo_id',
													'tbl_return.return_order_type','tbl_return.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    
													->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_return_details.return_cat_id')
                                                    ->join('tbl_return', 'tbl_return.return_order_id', '=', 'tbl_return_details.return_order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_return_details.return_product_id')

                                                    ->where('tbl_return.return_order_type','Confirmed')                        
                                                    ->where('tbl_return.fo_id',$foMainId)                        
                                                    ->where('tbl_return_details.return_order_id',$orderMainId)
                                                    ->where('tbl_return_details.return_cat_id', $items->catid)    
                                                    ->get();
                                                   
										//dd($reultPro);
										//echo '<pre/>'; print_r($reultPro); exit;
                                                
												@endphp
                                                
												@foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalQty += $itemsPro->return_qty;
                                                $totalPrice += $itemsPro->return_qty * $itemsPro->p_unit_price;

                                                @endphp

                                                <tr>
                                                <th>{{ $serial }}</th>
                                                <th></th>
                                                <th>{{ $itemsPro->proname }}</th>
                                                <th style="text-align: right;">{{ $itemsPro->return_qty }}</th>
                                                <th style="text-align: right;">{{ number_format($itemsPro->return_qty * $itemsPro->p_unit_price,0) }}</th>
                                                <th style="text-align: right;">-</th>
                                                <th style="text-align: right;">
                                                    <input type="text" name="qty[]" id="qty{{ $serial }}" value="{{ $itemsPro->return_qty }}" class="form-control" style="width: 100px;" onkeyup="qty({{ $serial }})" min="1" maxlength="8">
                                                </th>
                                                <th style="text-align: right;" id="rowPrice{{ $serial }}">{{ number_format($itemsPro->return_qty * $itemsPro->p_unit_price,0) }}</th>
                                                <input type="hidden" name="oldqty[]" id="oldqty{{ $serial }}" value="{{ $itemsPro->return_qty }}">
                                                <input type="hidden" name="price[]" id="price{{ $serial }}" value="{{ $itemsPro->p_unit_price }}" >
                                                <input type="hidden" name="product_id[]" id="product_id{{ $serial }}" value="{{ $itemsPro->return_product_id }}" >
                                                
                                                
                                                <th style="text-align: right;">
                                                    <input type="text" name="changeDelivery[]" id="changeDelivery{{ $serial }}" value="{{ $itemsPro->return_qty }}" class="form-control" style="width: 100px;">
                                                </th>
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
                                                <th style="text-align: right;">{{ $totalQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalPrice,0) }}</th>
                                                <th style="text-align: right;" id="totalQty">{{ $totalQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <th style="text-align: right;" id="totalPrice">{{ number_format($totalPrice,0) }}
                                                    <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                                                </th>
                                                <th>&nbsp;</th>                                            
                                            </tr>
                                            
                                           
                                           
                                        </tbody>
                                        <tfoot>
                                            
                                        </tfoot>
                                    </table>
                                    <p></p>
                                    
									
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->return_order_id }}">
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

    
@endsection