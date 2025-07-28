@extends('ModernSales::masterPage')
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
            
            <form action="{{ URL('/mts-return-delivery-edit-submit') }}" method="POST">
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
                                           {{--  PO No : {{ $resultInvoice->po_no }}<br /> --}}
                                           <br /> 
                                           Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                            @if($customerInfo->route_id==1)
                                            Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="col-sm-4">
                                        <span>
                                            
                                            Return No : {{ $resultInvoice->return_no }}<br />
                                            Collected By &nbsp; : {{$resultInvoice->display_name}}<br /> 
                                            Date &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->return_date }}
                                        </span>
                                    </div>
                                </div>                                                           
                            </div>

                            <div class="header">
                                <h2>Return Delivery entry</h2>                            
                            </div>

                            <div class="body">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product Group</th>
                                                <th>Product Code</th>
                                                <th>Product Name</th>
                                                <th>Return Qty</th> 
                                                <th>Return Value</th>               
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
                                                <th colspan="5">{{ $items->catname }}</th>
                                            </tr>

                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('mts_return_details')
                                        ->select('mts_return_details.return_det_id','mts_return_details.cat_id','mts_return_details.return_id','mts_return_details.order_qty','mts_return_details.order_total_value','mts_return_details.product_id','mts_return_details.order_qty','mts_return_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.party_id','tbl_product.id','tbl_product.name AS proname','tbl_product.sap_code')
                                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
                                        ->join('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                                        ->join('tbl_product', 'tbl_product.id', '=', 'mts_return_details.product_id')

                                        ->where('mts_return.order_status','Confirmed')                        
                                        ->where('mts_return.fo_id',$foMainId)                        
                                        ->where('mts_return_details.return_id',$DeliveryMainId)
                                        ->where('mts_return_details.cat_id', $items->catid)    
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
                                                <td>{{ $itemsPro->sap_code }}</td>
                                                <td>{{ $itemsPro->proname }}</td>
                                                <td style="text-align: center;">{{ $itemsPro->order_qty }}</td>
                                                 <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,0) }}</td> 
                                                
                                               
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
                                                <th colspan="4" align="right">Net Total</th>
                                                <th style="text-align: center;">{{ $totalQty }}</th>
                                                <th style="text-align: center;">{{ number_format($totalPrice,0) }}</th>
                                                
                                                                                      
                                            </tr>
                                            
                                        </tbody>
                                       
                                    </table>
                                    <p></p>
                                    <div class="row" style="text-align: center;">
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Return</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->return_id }}">
                                    <input type="hidden" name="retailderid" id="retailderid" value="{{ $resultInvoice->party_id }}">
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