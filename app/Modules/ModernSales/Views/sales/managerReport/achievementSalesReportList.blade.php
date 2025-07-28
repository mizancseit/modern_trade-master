<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($resultOrderList) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Customer Name</th> 
                        <th>SAP Code</th>
                        <th>Target Value(Secondary)</th>
                        <th>Achieve Value(Secondary)</th>
                        <th>Achievement %</th>
                        <th>Visit Qty</th>
                        <th>PO Qty</th>
                        <th>PO Value</th>
                        <th>Primary Sales</th>
                        <th>PG Covered</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalTarget = 0;
                    $totalPoValue =0;
                    $totalPrimerySales = 0; 
                    $totalAchieveValue = 0; 
                    $totalVisitQty = 0;
                    $totalPoQty =0;

                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php

                    $target = DB::table('mts_target_upload') 
                    ->where('customer_id', $orders->customer_id) 
                    ->sum('value'); 

                    $primerySales = DB::table('mts_order')
                    ->where('order_status','Delivered') 
                    ->where('customer_id', $orders->customer_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(delivery_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->sum('total_delivery_value');

                    $achieveValue =DB::table('mts_outlet_payments')
                    ->where('ack_status','CONFIRMED') 
                    ->where('customer_id', $orders->customer_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->sum('net_amount');

                    $visit = DB::table('mts_visit_order') 
                    ->select(DB::raw('SUM(`order`) as order_qty'),DB::raw('SUM(`visit`) as visit_qty'))
                    ->where('customer_id', $orders->customer_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->first();

                    $poValue =DB::table('mts_order')
                    ->whereIn('order_status',['Confirmed','Delivered']) 
                    ->where('customer_id', $orders->customer_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->sum('total_order_value');

                    $pgCount = DB::table('mts_order_details')
                    ->Join('mts_order','mts_order_details.order_id','=','mts_order.order_id')
                    ->where('mts_order.order_status','Delivered') 
                    ->where('mts_order.customer_id', $orders->customer_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.delivery_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->groupBy('mts_order_details.cat_id')
                    ->get(); 

                    $newRowWisePgCount = count($pgCount);

                    $totalTarget  += $target;

                    $totalPoValue  += $poValue; 
                    $totalPrimerySales  += $primerySales; 
                    $totalAchieveValue  += $achieveValue; 
                    $totalVisitQty  += $visit->visit_qty + $visit->order_qty; 
                    $totalPoQty  += $visit->order_qty; 

                        if($target>0 && $achieveValue>0){
                            $achieveVariance = ($achieveValue/$target)*100;
                        }else{
                            $achieveVariance =0;
                        }

                    
                    @endphp                    
                    <tr>
                        <td>{{ $serial }}</td>
                        <td>{{$orders->name}}({{$orders->customer_id}})</td>
                        <td>{{$orders->sap_code}}</td> 
                        <td>{{number_format($target,2)}}</td>                    
                        <td>{{number_format($achieveValue,2)}}</td>                    
                                            
                        <td>{{number_format($achieveVariance,2)}} %</td>   
                        <td>@if(sizeof($visit)>0){{$visit->visit_qty + $visit->order_qty}}@else {{ 0 }} @endif</td>                 
                        <td>@if(sizeof($visit)>0){{$visit->order_qty}}@else {{ 0 }} @endif</td>                                    
                        <td>{{number_format($poValue,2)}}</td>                    
                        <td>{{number_format($primerySales,2)}}</td>                    
                        <td>{{$newRowWisePgCount}}</td>                    
						
						
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Grand Total : </th>           
                         <th>{{ number_format($totalTarget,2) }}</th>
                        <th>{{ number_format($totalAchieveValue,2) }}</th> 
                        <th>@if($totalAchieveValue>0 AND $totalTarget>0) {{number_format(($totalAchieveValue/$totalTarget) * 100,2) }} @else {{'0.00'}} @endif %</th>  
                         <th>{{ number_format($totalVisitQty,2) }}</th>
                        <th>{{ number_format($totalPoQty,2) }}</th> 
                        <th>{{ number_format($totalPoValue,2) }}</th> 
                        <th>{{ number_format($totalPrimerySales,2) }}</th> 
                        <th></th> 
                    </tr>

                @else
                    <tr>
                        <th colspan="9">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
