@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Advance Replace
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
            
            <form action="{{ URL('/mts-replace-delivery-edit-submit') }}" method="POST">
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
                                            
                                            Outlet Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $resultInvoice->name }}</span>
                                            <br /> 
                                            Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $customerInfo->address }}<br />
                                            @if($customerInfo->route_id==1)
                                            Shiping Address&nbsp;:&nbsp; {{ $resultInvoice->address }}
                                            @endif
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
                                <h2>Replace Delivery entry</h2>                            
                            </div>

                            <div class="body">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product Group</th>
                                                <th>Product Name</th>
                                                <th>Replace</th>
                                                {{-- <th>Value</th>  --}}
                                                <th>R.Products </th>
                                                <th>R.Delivery</th>
                                                {{-- <th>R.Value</th> --}}                   
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
                                                <th colspan="7">{{ $items->catname }}</th>
                                            </tr>

                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('mts_replace_details')
                                        ->select('mts_replace_details.replace_det_id','mts_replace_details.cat_id','mts_replace_details.replace_id','mts_replace_details.order_qty','mts_replace_details.order_total_value','mts_replace_details.product_id','mts_replace_details.order_qty','mts_replace_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.party_id','tbl_product.id','tbl_product.name AS proname')
                                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
                                        ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                                        ->join('tbl_product', 'tbl_product.id', '=', 'mts_replace_details.product_id')

                                        ->where('mts_replace.order_status','Confirmed')                        
                                        ->where('mts_replace.fo_id',$foMainId)                        
                                        ->where('mts_replace_details.replace_id',$DeliveryMainId)
                                        ->where('mts_replace_details.cat_id', $items->catid)    
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
                                                <th>{{ $serial }}</th>
                                                <th></th>
                                                <th>{{ $itemsPro->proname }}</th>
                                                <th style="text-align: center;">{{ $itemsPro->order_qty }}</th>
                                                {{-- <th style="text-align: center;">{{ number_format($itemsPro->order_total_value,0) }}</th> --}}
                                                <th>
                                                    @php
                                                    $depo_price = 0;

                                                    $resultProduct = DB::table('tbl_product')
                                                    ->select('id','name','depo')
                                                    ->where('status', '0')
                                                    ->where('category_id', $itemsPro->catid)
                                                    ->get();

                                                    @endphp

                                                    <div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
                                                        <select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
                                                            <option value="">Select Product</option>
                                                            <?php   foreach($resultProduct as $pname) {
                                                                if($itemsPro->product_id ==  $pname->id) { ?>
                                                                <option value="{{ $pname->id }}" selected="">{{ $pname->name }}</option>

                                                                <?php   

                                                                if($pname->depo!=''):
                                                                    $depo_price = $pname->depo;
                                                                else:
                                                                    $depo_price = 0;
                                                                endif;      

                                                            }   else { ?>
                                                            <option value="{{ $pname->id }}">{{ $pname->name }}</option>
                                                        <?php   } 
                                                            } ?>
                                                        </select>
                                                        <input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="{{$depo_price}}">
                                                    </div>  

                                                </th>
                                                <th style="text-align: right;">

                                                <input type="number" class="form-control" id="changeQty{{$serial}}" name="qty[]" value="{{ $itemsPro->order_qty }}" maxlength="3" style="width: 80px;" onkeyup="addChange({{$serial}})">
                                                </th>

                                                        <input type="hidden" class="form-control" id="changeValue{{$serial}}" name="price[]" value="{{$itemsPro->order_total_value}}" maxlength="8" style="width: 80px;"  readonly="">                          

                                                {{-- <th>
                                                   
                                                </th> --}}
                                               
                                                <input type="hidden" name="product_id[]" id="product_id{{ $serial }}" value="{{ $itemsPro->product_id }}" >
                                                <input type="hidden" name="replace_det_id[]" id="replace_det_id{{ $serial }}" value="{{ $itemsPro->replace_det_id }}" >
                                                
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
                                                
                                                <th>&nbsp;</th>
                                                <th style="text-align: center;" id="totalQty">{{ $totalQty }}</th>
                                                <input type="hidden" name="totalHiddenQty" id="totalHiddenQty" value="{{ $totalQty }}">
                                                <input type="hidden" name="totalHiddenPrice" id="totalHiddenPrice" value="{{ number_format($totalPrice,0) }}">
                                                <input type="hidden" name="totalDeliveryPrice" id="totalDeliveryPrice" value="{{ number_format($totalPrice,0) }}">
                                                
                                                                                      
                                            </tr>
                                            
                                        </tbody>
                                       
                                    </table>
                                    <p></p>
                                    <div class="row" style="text-align: center;">
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm Delivery</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="orderid" id="orderid" value="{{ $resultInvoice->replace_id }}">
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