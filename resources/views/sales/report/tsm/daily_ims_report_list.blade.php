<div class="card"  id="printMe">
    <div class="header">
        <h5>
            About {{ sizeof($allDepot) }} results 
        </h5>
    </div>                  
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
               <thead>
                    <tr style="font-size: 11px;">
                        <th>Point</th>
                        <th>FO Name</th>
                        <th>Monthly Target</th>
                        <th>On Date Sales (IMS Order Value)</th>
                        <th>On Date of Last Month (IMS Order Value)</th>
                        <th>On Date Sales (IMS Delivery Value)</th>
                        <th>Cumulative Sales (IMS Order Value)</th>
                        <th>Cumulative Last Month (IMS Order Value)</th>
                        <th>Cumulative Sales (IMS Del. Value)</th>
                        <th>Cumulative Last Month (IMS Del. Value)</th>
                        <th>Monthly Achievement (%)</th>
                        <th>Monthly Pending to Achieve (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($allDepot)>0)
                        @php
                        $serial = 1;
                        $TotalTarget = 0;
                        $OnDateSales = 0;
                        $OnDateofLastMonth = 0;
                        $OnDateSalesDelivery  = 0;
                        $CumulativeSalesNew = 0;
                        $CumulativeLastMonth = 0;
                        $CumulativeSalesDelivery = 0;
                        $CumulativeLastMonth = 0;
                        $MonthlyAchievement  = 0;
                        $MonthlyPendingtoAchieve   = 0;

                        $serial1 = 0;
                        @endphp
                        @foreach($allDepot as $points)
                        <tr style="font-size: 11px;">
                            <!-- <th> {{ $serial }}</th> -->
                            <th> @if($serial==1) {{ $points->point_name }} @endif</th>
                            <th> {{ $points->display_name }} </th>
                            <th style="text-align: right;">
                                @php
                                    
                                    $currentMonthTargetMain = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                                            ->where('employee_id', $points->email)
                                            ->whereDate('start_date', '>=', $currentMonthStart)
                                            ->whereDate('end_date', '<=', $currentMonthEnd2)
                                            ->groupBy('fo_id')
                                            ->sum('total_value');

                                    $dailyTarget = round($currentMonthTargetMain/26);
                                    $currentMonthTarget = $targetDays * $dailyTarget;

                                    $TotalTarget +=$currentMonthTargetMain;
                                    echo number_format($currentMonthTargetMain,2);
                                @endphp
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMS = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_value')
                                    ->where('order_type','!=','Ordered')
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
                                    ->sum('total_value');

                                    $OnDateSales += $ordersIMS;
                                    echo number_format($ordersIMS,2); //On Date Sales (IMS Order Value)
                                @endphp
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMS = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_value')
                                    ->where('order_type','!=','Ordered')
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($sameDayLastMonth, $sameDayLastMonth))
                                    ->sum('total_value');

                                    $OnDateofLastMonth += $ordersIMS;

                                    echo number_format($ordersIMS,2); //On Date of Last Month (IMS Order Value)
                                @endphp
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMSLastMonth = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_delivery_value')
                                    //->where('order_type','=','Delivered')
                                    ->where('total_delivery_qty','>',0)
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
                                    ->sum('total_delivery_value');

                                    $OnDateSalesDelivery += $ordersIMSLastMonth;
                                    echo number_format($ordersIMSLastMonth,2);  //On Date Sales (IMS Delivery Value)

                                @endphp                                
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMS = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_value')
                                    ->where('order_type','!=','Ordered')
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($currentMonthStart, $currentMonthEnd))
                                    ->sum('total_value');

                                    $CumulativeSalesNew +=$ordersIMS;
                                    echo number_format($ordersIMS,2);  //Cumulative Sales (IMS Order Value)

                                @endphp                                
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMS = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_value')
                                    ->where('order_type','!=','Ordered')
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($sameDayLastMonthStart, $sameDayLastMonth))
                                    ->sum('total_value');

                                    $CumulativeLastMonth += $ordersIMS; 
                                    echo number_format($ordersIMS,2);  //Cumulative Last Month (IMS Order Value)
                                @endphp                                
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $CumulativeSalesD = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_delivery_value')
                                    //->where('order_type','=','Delivered')
                                    ->where('total_delivery_qty','>',0)
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart, $fromdate))
                                    ->sum('total_delivery_value');

                                    $CumulativeSalesDelivery += $CumulativeSalesD;
                                    echo number_format($CumulativeSalesD,2);  //Cumulative Sales (IMS Del. Value)
                                @endphp                                
                            </th>
                            <th style="text-align: right;"> 
                                @php
                                    $ordersIMSLastMonth = DB::table('tbl_order')
                                    ->select('fo_id','route_id','total_delivery_value')
                                    //->where('order_type','=','Delivered')
                                    ->where('total_delivery_qty','>',0)
                                    ->where('fo_id',$points->id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonthStart, $sameDayLastMonth))
                                    ->sum('total_delivery_value');

                                    $CumulativeLastMonth += $ordersIMSLastMonth;
                                    echo number_format($ordersIMSLastMonth,2);  //Cumulative Last Month (IMS Del. Value)
                                @endphp                                
                            </th>
                            <th style="text-align: right;"> 
                                @php 

                                    if($currentMonthTargetMain >0 && $CumulativeSalesD >0)
                                    {
                                        
                                        $MonthlyAchievement += $achievement = (($CumulativeSalesD)/($currentMonthTargetMain) * 100);
                                        echo number_format(($CumulativeSalesD)/($currentMonthTargetMain) * 100,2).'%';
                                    }
                                    else
                                    {
                                        echo $achievement = 0;
                                    }     
                                     
                                @endphp                                
                            </th>                            
                            <th style="text-align: right;"> 
                                 @php 
                                     $MonthlyPendingtoAchieve += (100 - $achievement);                               
                                     echo number_format((100 - $achievement),2).'%';
                                @endphp
                            </th>                                
                        </tr>
                        @php
                        $serial ++;
                        $serial1++;
                        @endphp
                        @endforeach
                    @endif

                    <tr style="font-size: 11px;">
                        <th colspan="2">Grand Total</th>
                        <th style="text-align: right;">{{ number_format($TotalTarget,2)}} </th>
                        <th style="text-align: right;">{{ number_format($OnDateSales,2)}}</th>
                        <th style="text-align: right;">{{ number_format($OnDateofLastMonth,2)}}</th>
                        <th style="text-align: right;">{{ number_format($OnDateSalesDelivery,2)}}</th>
                        <th style="text-align: right;">{{ number_format($CumulativeSalesNew,2)}}</th>
                        <th style="text-align: right;">{{ number_format($CumulativeLastMonth,2)}}</th>

                        <th style="text-align: right;">{{ number_format($CumulativeSalesDelivery,2)}}</th>
                        <th style="text-align: right;">{{ number_format($CumulativeLastMonth,2)}}</th>

                        <th style="text-align: right;">
                            @php
                            $m = ($CumulativeSalesDelivery/$TotalTarget);
                            @endphp
                            {{ number_format(($CumulativeSalesDelivery/$TotalTarget)*100,2).'%' }}

                            <!-- {{ $CumulativeSalesDelivery.'__'.$TotalTarget }} -->
                        <!-- {{ number_format($MonthlyAchievement/$serial1,2).'%'}} -->
                            
                        </th>
                        <th style="text-align: right;">
                            {{ number_format((1-$m)*100,2).'%' }}
                            <!-- {{ number_format(($CumulativeSalesDelivery/$TotalTarget),2).'%' }} -->
                        <!-- {{ number_format( $MonthlyPendingtoAchieve/$serial1,2).'%'}} -->
                    </th>
                    </tr>

                </tbody> 
            </table>
        </div>
    </div>

    {{-- For Print --}}
    @if(sizeof($allDepot) > 0)
    <div class="card">
        <div class="row" style="text-align: center; padding: 10px 10px; ">
            <div class="col-sm-12">
                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                    <i class="material-icons">print</i>
                    <span>PRINT...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>