@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Invoice
                             <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="font-weight:">
                         <div class="body" id="printMe" >
                            <table width="100%">
                                    <thead>
                                        <tr>
                                            <th width="70%" height="49" align="left" valign="top">
                                            {{ $resultDistributorInfo->first_name }} <br />
                                            Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Route &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                            Mobile &nbsp; : {{ $resultDistributorInfo->cell_phone }}
                                            <p></p>
                                            To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                            Mobile &nbsp; : {{ $resultInvoice->mobile }}
                                            
                                            {{-- <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo"> --}}
                                            </th>
                                            <th width="30%" align="left" valign="top">
                                            @if($resultInvoice->order_type=='Delivered')  
                                            INVOICE <br />
                                            Delivery Date : {{ $resultInvoice->update_date }}
                                            @else 
                                            MEMO 
                                            @endif  
                                            <br />

                                            @if($resultInvoice->order_type=='Delivered')
                                            Order No
                                            @else 
                                            Memo No 
                                            @endif
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_no }}

                                            <p></p>
                                            Collected By : {{ $resultFoInfo->first_name }} <br />
                                            Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }} <br />
                                            @if($resultInvoice->order_type=='Delivered')
                                            Order Date
                                            @else 
                                            Memo Date 
                                            @endif
                                             &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->order_date }}

                                            </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        
                                        
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>SL</th>
                                      <th>Product Group</th>
                                      <th>Product Name</th>
                                      <th>Wastage Qty</th>                
                                      <!-- <th>Replace Delivery</th> -->
                                     
                                    </tr>
                                  </thead>

                                  <tbody>
                                            
                                            @if(sizeof($resultCartPro)>0)
                                            @php
                                            $serial   = 1;
                                            $count    = 1;
                                            $subTotal = 0;
                                            $totalWastage = 0;
                                            
                                            $totalReplaceQty = 0;
                                            $totalDeliveryQty = 0;
                                          

                                            @endphp
                                            @foreach($resultCartPro as $items)                                       
                                            <tr>
                                                <th></th>
                                                <th colspan="3">{{ $items->catname }}</th>
                                            </tr>
                                                @php 
                                                $itemsCount = 1;
                                        $reultPro  = DB::table('tbl_wastage_details')
                                                    ->select('tbl_wastage_details.replace_delivered_qty','tbl_wastage_details.order_det_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_total_price','tbl_wastage_details.product_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_unit_price','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.retailer_id','tbl_product.id','tbl_product.name AS proname')
                                                    ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                                                    ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')

                                                    ->where('tbl_wastage.order_type','Confirmed')                        
                                                    //->where('tbl_wastage.fo_id',$foMainId)                        
                                                    ->where('tbl_wastage_details.order_id',$orderMainId)
                                                    ->where('tbl_wastage_details.cat_id', $items->catid)    
                                                    ->get();
                                                   //dd($reultPro);
                                                @endphp
                                                @foreach ($reultPro as $itemsPro)
                                                @php
                                                $subTotal += $itemsPro->p_total_price;
                                                $totalWastage += $itemsPro->wastage_qty;
                                                $totalReplaceQty += $itemsPro->replace_delivered_qty;
                                               

                                                @endphp

                                                <tr>
                                                    <th>{{ $serial++ }}</th>
                                                    <th></th>
                                                    <th>{{ $itemsPro->proname }}</th>
                                                   
                                                    @if(Auth::user()->user_type_id==5)
                                                                                                  
                                                    <th style="text-align: right;">@if($itemsPro->wastage_qty==null) - @else {{ $itemsPro->wastage_qty }} @endif</th>

                                                    <!-- <th style="text-align: right;">@if($itemsPro->replace_delivered_qty==null) - @else {{ $itemsPro->replace_delivered_qty }} @endif</th> -->
                                                    @endif
                                                </tr>

                                                @endforeach

                                                                                           
                                           
                                            @endforeach
                                            @endif

                                            <tr>
                                                <th colspan="3" align="right" class="bg-light-green">Total</th>
                                                <th style="text-align: right;" class="bg-light-green">{{ $totalWastage }}</th>
                                                <!-- <th style="text-align: right;" class="bg-light-green">{{ $totalReplaceQty }}</th> -->
                                                                                  
                                            </tr>
                                            
                                           
                                            
                                        </tbody>
                                                                        
                           </table>
                        </div>

                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printWastageReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>
                        <p>&nbps;</p>

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection