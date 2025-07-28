
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
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y',strtotime('-2 Day')) }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <select id="pointsID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Point </option> 
                                @foreach($resultPoint as $points)
                                    <option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> FO </option> 
                                @foreach($resultFO as $fos)
                                    <option value="{{ $fos->fo_id }}">{{ $fos->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

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
                                        <th style="text-align: right;">Offer Qty</th>
                                        <th style="text-align: right;">Offer Value</th>
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
                                $totalOfferQty =0;
                                $totalOfferQty1 =0;
                                $totalOfferQty2 =0;
                                $totalOfferQty3 =0;
                                $totalOfferValue =0;
                                $totalOfferValue1 =0;
                                $totalOfferValue2 =0;
                                $totalOfferValue3 =0;
                                @endphp
                                @foreach($resultIms as $orders)
                                @php                                
                                $totalMemo += $orders->totalMemo;
                                $totalQty +=$orders->orderQty;
                                $totalValue +=$orders->orderValue;

                                $totalDQty +=$orders->deliveryQty;
                                $totalDValue +=$orders->deliveryValue;

                                $pDate = date('Y-m-d',strtotime('-2 Day'));
                                $SpecialFreeQty = DB::table('tbl_order_special_free_qty')
                                        ->select('tbl_order_special_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                                        ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($pDate, $pDate))
                                        
                                        ->where('catid',$orders->cat_id)
                                        ->whereIn('status', array('0','3'))
                                        ->groupBy('catid')
                                        ->first();

                                //print_r($SpecialFreeQty);

                                if(sizeof($SpecialFreeQty)>0)
                                {
                                    $totalOfferQty = $SpecialFreeQty->freeQty;
                                    $totalOfferValue = $SpecialFreeQty->freeValue;
                                }

                                $SpecialFreeAndQty = DB::table('tbl_order_special_and_free_qty')
                                        ->select('tbl_order_special_and_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                                        ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($pDate, $pDate))
                                        
                                        ->where('catid',$orders->cat_id)
                                        ->groupBy('catid')
                                        ->first();

                                if(sizeof($SpecialFreeAndQty)>0)
                                {
                                    $totalOfferQty1 = $SpecialFreeAndQty->freeQty;
                                    $totalOfferValue1 = $SpecialFreeAndQty->freeValue;
                                }

                                $RegularFreeAndQty = DB::table('tbl_order_regular_and_free_qty')
                                        ->select('tbl_order_regular_and_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                                        ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($pDate, $pDate))
                                        
                                        ->where('catid',$orders->cat_id)
                                        ->groupBy('catid')
                                        ->first();

                                if(sizeof($RegularFreeAndQty)>0)
                                {
                                    $totalOfferQty2 = $RegularFreeAndQty->freeQty;
                                    $totalOfferValue2 = $RegularFreeAndQty->freeValue;
                                }

                                $totalOfferQty3 += $totalOfferQty + $totalOfferQty1 + $totalOfferQty2;
                                $totalOfferValue3 += $totalOfferValue + $totalOfferValue1 + $totalOfferValue2;

                                @endphp    
                                    <tr style="font-size: 13px;">
                                        <th>{{ $s }}</th>
                                        <th> {{ date('d-m-Y',strtotime($orders->update_date)) }}</th>
                                        <th>{{ $orders->point_name }}</th>                        
                                        <th>{{ $orders->display_name }}</th>
                                        <th>{{ $orders->name }}</th>
                                        <th style="text-align: right;">{{ $orders->deliveryQty }}</th>
                                        <th style="text-align: right;">{{ $orders->deliveryValue }}</th>
                                        <th style="text-align: right;"> {{ $totalOfferQty + $totalOfferQty1 + $totalOfferQty2 }}</th>
                                        <th style="text-align: right;"> {{ $totalOfferValue }}</th>

                                        <!-- <th style="text-align: right;">{{ $orders->orderQty }}</th>
                                        <th style="text-align: right;">{{ $orders->orderValue }}</th> -->
                                        <th style="text-align: right;">{{ $orders->totalMemo }}</th>
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
                                        <th style="text-align: right;"><!-- {{ $totalOfferQty3 }} --></th>
                                        <th style="text-align: right;"><!-- {{ number_format($totalOfferValue3,2) }} --></th>
                                        <!-- <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th> -->
                                        <th style="text-align: right;">{{ $totalMemo }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection