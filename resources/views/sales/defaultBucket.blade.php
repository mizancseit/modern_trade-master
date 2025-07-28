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
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Visit </a> / <a href="{{ URL('/new-order') }}"> New Order </a> / Bucket
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>ALL BUCKET PRODUCT</h2>                            
                        </div>
                        <div class="body">
                            <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Order Qty</th>
                                            <th>Value</th>
                                            <th>Wastage Qty</th>                                            
                                            <th>Edit</th>                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" align="right">NET AMMOUNT</th>
                                            <th>&nbsp;</th>
                                            <th>19,997</th>
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        
                                        <tr>
                                            <th colspan="7">{{ $resultRetailer->name }}</th>                  
                                        </tr>

                                        @if(sizeof($resultCartPro)>0)
                                        @php
                                        $serial = 1;
                                        @endphp
                                        @foreach($resultCartPro as $items)
                                        <tr>
                                            <th>{{ $serial }}</th>
                                            <th>{{ $items->catname }}</th>
                                            @php 
                                            $itemsCount = 1;
                                    $reultPro  = DB::table('tbl_order_details')
                                                ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_qty','tbl_order_details.p_total_price','tbl_order_details.product_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')

                                                ->where('tbl_order.order_type','Ordered')                        
                                                ->where('tbl_order.fo_id',Auth::user()->id)                        
                                                ->where('tbl_order.retailer_id',$retailderid)
                                                ->where('tbl_order_details.cat_id', $items->catid)    
                                                ->get();
                                               //dd($reultPro);
                                            @endphp
                                            @foreach ($reultPro as $itemsPro) 
                                            <th>{{ $itemsPro->proname }}</th>
                                            <th>{{ $itemsPro->order_qty }}</th>
                                            <th>{{ $itemsPro->p_total_price }}</th>
                                            <th>0</th>                                            
                                            <th><input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-lg waves-effect"></th>
                                            
                                            @php
                                            $itemsCount ++;
                                            @endphp
                                            @endforeach                                            
                                        </tr>
                                        @php
                                        $serial ++;
                                        @endphp
                                        @endforeach
                                        @endif

                                        <tr>
                                            <th colspan="4" align="right">Sub Total</th>
                                            <th>20</th>
                                            <th>20000</th>
                                            <th>0</th>                                            
                                        </tr>
                                        <tr>
                                            <th colspan="4" align="right">Grand Total</th>
                                            <th>20</th>
                                            <th>20000</th>
                                            <th>0</th>                                            
                                        </tr>
                                        <tr>
                                            <th colspan="4" align="left">MEMO COMMISSION : 2.50%</th>
                                            <th>&nbsp;</th>
                                            <th>3</th>
                                            <th>&nbsp;</th>                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <p></p>
                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-3">
                                        <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Order Request</button>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn bg-red btn-block btn-lg waves-effect">Delete</button>
                                    </div>
                                </div>

                                
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection