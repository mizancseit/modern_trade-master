<div class="card" id="printMe">
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
                        <th>Month</th>
                        <th>Point</th>
                        <th>FO Name</th>
                        <th style="text-align: right;">Target (Value)</th>
                        <th style="text-align: right;">IMS Value</th>                        
                        <th style="text-align: right;">Monthly Achievement (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($allDepot)>0)
                        @php
                        $serial = 1;
                        $totalTarget = 0;
                        $totalIMS = 0;
                        @endphp

                        @for($i=$monthStart;$i<= $monthEnd;$i++)
                            @foreach($allDepot as $points)
                                
                                <tr style="font-size: 11px;">
                                    <!-- <th> {{ $serial }}</th> -->
                                    <th> 
                                @if($i=='01')
                                    January
                                @elseif($i=='01')
                                    February
                                @elseif($i=='02')
                                    February
                                @elseif($i=='03')
                                    March
                                @elseif($i=='04')
                                    April
                                @elseif($i=='05')
                                    May
                                @elseif($i=='06')
                                    June
                                @elseif($i=='07')
                                    July
                                @elseif($i=='08')
                                    August
                                @elseif($i=='09')
                                    September
                                @elseif($i=='10')
                                    October
                                @elseif($i=='11')
                                    November
                                @elseif($i=='12')
                                    December
                                @endif
                            </th>
                                    <th> @if($serial==1) {{ $points->point_name }} @endif</th>
                                    <th> {{ $points->display_name }} </th>
                                    <th style="text-align: right;">
                                        @php
                                        if(($i <=9))
                                        { 
                                            $m = '0'.$i;
                                        }
                                        else
                                        {
                                            $m = $i;
                                        }                                        

                                        $currentMonthStart = date('Y-'.$m.'-01');                                        
                                        $currentMonthEnd   = date('Y-'.$m.'-31');
                                        

                                        $currentMonthTarget = DB::table('tbl_fo_target')
                                        //->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('employee_id', $points->email)
                                        ->whereDate('start_date','>=',$currentMonthStart)
                                        ->whereDate('end_date','<=',$currentMonthEnd)
                                        ->sum('total_value');

                                        echo number_format($currentMonthTarget,2);
                                        $totalTarget += $currentMonthTarget;
                        
                                        @endphp
                                    </th>
                                    <th style="text-align: right;"> 
                                         @php
                                            $ordersIMSLastMonth = DB::table('tbl_order')
                                            ->select('fo_id','route_id','total_delivery_value')
                                            //->where('order_type','=','Delivered')
                                            ->where('total_delivery_qty','>',0)
                                            ->where('fo_id',$points->id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart, $currentMonthEnd))
                                            ->sum('total_delivery_value');

                                            echo number_format($ordersIMSLastMonth,2);  //On Date Sales (IMS Delivery Value)
                                            $totalIMS += $ordersIMSLastMonth;
                                        @endphp 
                                    </th>
                                    
                                    <th style="text-align: right;"> 
                                        @php 
                                            if($currentMonthTarget >0)
                                            {
                                                
                                                echo number_format(($ordersIMSLastMonth)/($currentMonthTarget) * 100,2).'%';
                                            }
                                            else
                                            {
                                                echo '0.00%';
                                            }
                                        @endphp                                
                                    </th>                                
                                </tr>                               

                            @php
                            $serial ++;
                            @endphp
                            @endforeach
                        @endfor

                        <tr style="font-size: 11px;">
                            <th colspan="3">Total</th>
                            <th style="text-align: right;">{{ number_format($totalTarget,2) }}</th>
                            <th style="text-align: right;">{{ number_format($totalIMS,2) }}</th>                        
                            <th style="text-align: right;">{{ number_format(($totalIMS/$totalTarget)*100,2).'%' }}</th>
                        </tr>

                    @endif

                </tbody> 
            </table>
        </div>
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