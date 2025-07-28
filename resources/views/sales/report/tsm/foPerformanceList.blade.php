<div class="card">  
    <div class="header">
        <h5>
            About {{ sizeof($userslist) }} results 
        </h5>
    </div>
    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr style="font-size: 11px;">
                                        <th>POINT</th>
                                        <th>FO</th>
                                        <th style="text-align: right;">No. of Route Covered</th>
                                        <th style="text-align: right;">No. of Retailer of Route</th>    
                                        <th style="text-align: right;">No. of Shop Visit</th>
                                        <th style="text-align: right;">No. of Order</th>                                        
                                        <th style="text-align: right;">Order Value</th>                                        
                                        <th style="text-align: right;">Del. Value</th>
                                        <th style="text-align: right;">Total Group Order (TGO)</th>
                                    </tr>
                                </thead>
                                
                                <tbody style="font-size: 13px;">
                                @if(sizeof($userslist) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalValue = 0;
                                    $totalDelQty = 0;  
                                    $totalDelValue = 0;  
                                    $totalVisit = 0;  
                                    $totalOrder = 0; 
                                    $totalRetailer = 0; 
                                    $routeTotalRetailer = 0;                                  
                                    $totalOrderGroup = 0;                                  
                                    $totalRoutesCovered = 0;                                  

                                    @endphp

                                    @foreach($userslist as $users)
                                    @php

                                    $totalGroupOrder = DB::table('tbl_order_details')
                                                    ->select('tbl_order_details.order_id','tbl_order_details.cat_id','tbl_order.order_id','tbl_order.fo_id','tbl_order.route_id','tbl_order.order_date','tbl_order.point_id')
                                                    ->join('tbl_order','tbl_order_details.order_id','=','tbl_order.order_id')
                                                    ->where('tbl_order.fo_id',$users->id)
                                                    ->where('tbl_order.point_id',$users->point_id)
                                                    //->where('tbl_order.order_type','Delivered')
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->groupBy('tbl_order_details.cat_id') 
                                                    ->count();

                                    $orders = DB::table('tbl_order')
                                                    ->select('fo_id','route_id',DB::raw('SUM(tbl_order.total_qty) as total_qty'),DB::raw('SUM(tbl_order.total_value) as total_value'))
                                                    ->where('order_type','!=','Ordered')
                                                    //->where('total_delivery_qty','>',0)
                                                    ->where('fo_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->orderBy('tbl_order.order_date','DESC') 
                                                    ->first();

                                    $routeID = DB::table('tbl_route')->select('point_id','route_id','rname')
                                                    ->where('point_id',$users->point_id)
                                                    ->first();

                                    $totalRouteName = DB::table('tbl_route')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('route_id',$routeID->route_id)
                                                    ->first();

                                    $totalRouteRetailer = DB::table('tbl_retailer')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('rid',$routeID->route_id)
                                                    ->where('status',0)
                                                    ->count('rid');

                                    $totalDalivery = DB::table('tbl_order')
                                                    ->select('fo_id',DB::raw('SUM(total_delivery_qty) as total_delivery_qty'),DB::raw('SUM(total_delivery_value) as total_delivery_value'))
                                                    //->where('order_type','Delivered')
                                                    ->where('total_delivery_qty','>',0)
                                                    ->where('fo_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->first();

                                    $totalVistFo = DB::table('ims_tbl_visit_order')
                                                    ->where('status',2)
                                                    ->where('foid',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->sum('visit');

                                    $totalOrderFo = DB::table('ims_tbl_visit_order')
                                                    ->where('status',3)
                                                    ->where('foid',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->sum('order');

                                    $totalRetailerCount = DB::table('tbl_retailer')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('status',0)
                                                    ->count('point_id');

                                    $totalRouteCovered = DB::table('ims_tbl_visit_order')
                                                    ->where('foid',$users->id)
                                                    //->where('routeid',$routeID->route_id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($fromdate, $todate))
                                                    ->count();

                                    if(sizeof($orders)>0)
                                    {
                                        $totalQty  += $orders->total_qty;
                                        $totalValue += $orders->total_value;
                                    } 
                                       
                                    if(sizeof($totalDalivery)>0)
                                    {                                                
                                        $totalDelQty  += $totalDalivery->total_delivery_qty;
                                        $totalDelValue += $totalDalivery->total_delivery_value;
                                    }          
      
                                    $totalVisit +=$totalVistFo + $totalOrderFo;
                                    $totalOrder += $totalOrderFo;
                                    $totalRetailer += $totalRetailerCount;
                                    $routeTotalRetailer += $totalRouteRetailer;
                                    $totalOrderGroup += $totalGroupOrder;
                                    $totalRoutesCovered += $totalRouteCovered;

                                    @endphp                    
                                    <tr style="font-size: 11px;">                                  
                                        <th>@if($serial==1) {{ $users->point_name }} @endif</th>
                                        <th>{{ $users->display_name }}</th>                
                                        <th style="text-align: right;">{{ $totalRouteCovered }} </th>                
                                        <th style="text-align: right;">{{ $totalRetailerCount }}</th>                                        
                                        <th style="text-align: right;">{{$totalVistFo + $totalOrderFo}}</th>
                                        <th style="text-align: right;">{{$totalOrderFo}}</th>
                                        <th style="text-align: right;">@if(sizeof($orders->total_qty)>0){{ number_format($orders->total_value,2) }} @else {{'0'}} @endif</th>
                                        <th style="text-align: right;">@if(sizeof($totalDalivery->total_delivery_qty)>0){{ number_format($totalDalivery->total_delivery_value,2) }} @else {{ '0' }} @endif</th>
                                        <th style="text-align: right;"> {{ $totalGroupOrder }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="2" style="text-align: right;">Total </th>
                                        <th style="text-align: right;"> {{ number_format($totalRoutesCovered,0)}} </th>
                                        <th style="text-align: right;"> </th>
                                        <th style="text-align: right;"> {{ number_format($totalVisit,0)}} </th>
                                        <th style="text-align: right;"> {{ number_format($totalOrder,0)}} </th>
                                        <th style="text-align: right;"> {{ number_format($totalValue,2)}} </th>
                                        <th style="text-align: right;"> {{ number_format($totalDelValue,2)}} </th>
                                        <th style="text-align: right;"> {{ number_format($totalOrderGroup,0)}} </th>
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="16">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
</div>