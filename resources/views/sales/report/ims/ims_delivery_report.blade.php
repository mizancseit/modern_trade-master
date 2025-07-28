
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        IMS DELIVERY REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
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

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>IMS DELIVERY REPORT </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                           <select id="channel" name="channel" class="form-control show-tick" data-live-search="true">
                                <option value="1">Lighting</option> 
                                <option value="2">Accessories</option> 
                                <option value="3">Fan</option>           
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <select id="divisions" name="divisions" class="form-control show-tick" data-live-search="true" onchange="foPerformancePoints()">
                                <option value="">Choose Division</option> 
                                @foreach($resultDivision as $divi)
                                    <option value="{{ $divi->div_id }}">{{ $divi->div_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2" id="fopoints">
                            <select name="pointsID" id="pointsID" class="form-control show-tick" data-live-search="true" onchange="getFoPerformanceList(this.value)">
                                <option value=""> Select Point</option>                
                            </select>
                        </div>
                       

                        <div class="col-sm-2" id="foID1">
                            <select id="foID" name="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Select FO </option> 
                                                                                  
                            </select>
                        </div>

                        {{-- <div class="col-sm-3">
                            <select id="pointsID" class="form-control show-tick" data-live-search="true" onchange="getFoList(this.value)">
                                <option value=""> Point </option> 
                                @foreach($resultPoint as $points)
                                    <option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3" id="foID1">
                            <select id="foID" name="foID" >
                                <option value=""> SELECT FO </option> 
                                                                                  
                            </select>
                        </div> --}}

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allFoOrderImsDelivery()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultIms) }} results 
                        </h5>
                    </div>                  
                    <div class="body">
                        <div class="table-responsive">
                        @if(sizeof($resultIms)>0)
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Group</th>
                                        <th style="text-align: right;">D.Qty</th>
                                        <th style="text-align: right;">D.Value</th>
                                        {{-- <th style="text-align: right;">Offer Qty</th>
                                        <th style="text-align: right;">Offer Value</th> --}}
                                        <!-- <th style="text-align: right;">Order Qty</th>
                                        <th style="text-align: right;">Order Value</th> -->
                                        <th style="text-align: right;">Memo</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @php
                                $s=1;
                                $totalMemo =0;
                                $totalQty =0;
                                $totalValue =0;
                                $totalDQty =0;
                                $totalDValue =0;
                                @endphp
                                @foreach($resultIms as $orders)
                                @php                                
                                $totalMemo += $orders->totalMemo;
                                $totalQty +=$orders->orderQty;
                                $totalValue +=$orders->orderValue;

                                $totalDQty +=$orders->deliveryQty;
                                $totalDValue +=$orders->deliveryValue;

                                $pDate = date('Y-m-d',strtotime('-2 Day'));
                               

                                @endphp    
                                    <tr style="font-size: 13px;">
                                        <td>{{ $s }}</th>
                                        <td> {{ date('d-m-Y',strtotime($orders->update_date)) }}</td>
                                        <td>{{ $orders->point_name }}</td>                        
                                        <td>{{ $orders->display_name }}</td>
                                        <td>{{ $orders->name }}</td>
                                        <td style="text-align: right;">{{ $orders->deliveryQty }}</td>
                                        <td style="text-align: right;">{{ $orders->deliveryValue }}</td>
                                       
                                        <td style="text-align: right;">{{ $orders->totalMemo }}</td>
                                    </tr>
                                @php
                                $s++;
                                @endphp                                   
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right;">Total</th>                                    
                                        <th style="text-align: right;">{{ $totalDQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDValue,2) }}</th>
                                       
                                        <th style="text-align: right;">{{ $totalMemo }}</th>
                                    </tr>
                                </tfoot>
                            </table>


                            <!---- offer show start -->
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Slab</th>
                                        <th>Group</th>
                                        <th>Slab Qty</th>
                                        <th>Free Qty</th>
                                        <th>Free Value</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                               @php 
                               $i=1;
                               $totalFreeQty = 0;
                               $totalFreeValue = 0;
                               @endphp
                                @foreach($freeSlab as $freeSlab)
                                
                                    <tr style="font-size: 13px;">
                                        <th>{{$i++}}</th>
                                        <th> {{ date('d-m-Y',strtotime($freeSlab->deliverydate)) }}</th>
                                        <th>{{ $freeSlab->point_name }}</th>                        
                                        <th>{{ $freeSlab->display_name }}</th>
                                        <th colspan="5">{{ $freeSlab->slab }}</th>
                                    </tr>

                                    @php

                                    $freeQty =  DB::select("SELECT date(e.order_date) AS date,e.fo_id,e.slab,sum(e.slab_count) AS slab_qty,e.catid,tbl_product_category.name,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,date(e.delivery_date) AS deliverydate FROM (
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,total_free_value,delivery_date FROM tbl_order_special_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,total_free_value,delivery_date FROM tbl_order_special_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,total_free_value,delivery_date FROM tbl_order_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,total_free_value,delivery_date FROM tbl_order_regular_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            ) AS e 
                                            join tbl_product_category on tbl_product_category.id=e.catid
                                            group by e.slab,e.catid ORDER BY e.slab");

                                    @endphp

                                    @foreach($freeQty as $freeQty)

                                        <tr style="font-size: 13px;">
                                            <td colspan="5"></td>
                                            <td>{{ $freeQty->name }}</td>
                                            <td>{{ $freeQty->slab_qty }}</td>                            
                                            <td>{{ $freeQty->qty }}</td>
                                            <td>{{ $freeQty->value }}</td>
                                        </tr>

                                    @endforeach
                                @php
                                $s++;
                                 $totalFreeQty += $freeQty->qty;
                                $totalFreeValue += $freeQty->value;
                                @endphp                                   
                                @endforeach
                                <tr>
                                    <th colspan="7"  style="text-align: right;">Total</th>
                                    <th>{{ $totalFreeQty }}</th>
                                    <th>{{ number_format($totalFreeValue,2) }}</th>
                                </tr>
                                </tbody>
                                
                            </table>
                        @else
                        <table class="table">
                              
                                    <tr>
                                        <th>Data no available</th>
                                    </tr>
                                
                        </table>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection