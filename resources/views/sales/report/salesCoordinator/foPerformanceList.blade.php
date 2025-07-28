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
                                    <tr>
                                        <th>SL</th>
                                        <th>DATE</th>
                                        <th>CHANNEL</th>
                                        <th>DIV</th>
                                        <th>POINT</th>
                                        <th>FO</th>
                                        <th>EMP.ID</th>
                                        <th>TOTAL&nbsp;RETAILER</th>
                                        <th>ROUTE&nbsp;NAME</th>
                                        <th>NO.&nbsp;OF&nbsp;RETAILER</th>
                                        <th>NO.VISIT</th>
                                        <th>NO.ORDER</th>
                                        <th>O.QTY</th>
                                        <th>O.VALUE</th>
                                        <th>DEL.QTY</th>
                                        <th>DEL.VALUE</th>
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

                                    @endphp

                                    @foreach($userslist as $users)
                                    @php

                                    $orders = DB::table('tbl_order')
                                                    ->select('fo_id','route_id',DB::raw('SUM(tbl_order.total_qty) as total_qty'),DB::raw('SUM(tbl_order.total_value) as total_value'))
                                                    ->where('order_type','!=','Ordered')
                                                    ->where('fo_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                                                    ->orderBy('tbl_order.order_date','DESC') 
                                                    ->first();

                                    $attendanceRoute = DB::table('ims_attendence')
                                                    ->where('foid',$users->id)
                                                    ->where('date',$todate)
                                                    ->where('type',1)
                                                    ->first();


                                     $attRoute = 0;
                                      if(sizeof($attendanceRoute)>0){
                                        $attRoute = $attendanceRoute->routes;
                                     }

                                     $totalRouteName = DB::table('tbl_route')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('route_id',$attRoute)
                                                    ->where('status',0)
                                                    ->first();

                                    $totalRouteRetailer = DB::table('tbl_retailer')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('rid',$attRoute)
                                                    ->where('status',0)
                                                    ->count('rid');

                                    $totalDalivery = DB::table('tbl_order')
                                                    ->select('fo_id',DB::raw('SUM(total_delivery_qty) as total_delivery_qty'),DB::raw('SUM(total_delivery_value) as total_delivery_value'))
                                                    //->where('order_type','Delivered')
                                                    ->where('total_delivery_qty','>',0)
                                                    ->where('fo_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($todate, $todate))
                                                    ->first();

                                    $totalVistFo = DB::table('ims_tbl_visit_order')
                                                    ->where('status',2)
                                                    ->where('foid',$users->id)
                                                    ->where('date',$todate)
                                                    ->sum('visit');
                                    $totalOrderFo = DB::table('ims_tbl_visit_order')
                                                    ->where('status',3)
                                                    ->where('foid',$users->id)
                                                    ->where('date',$todate)
                                                    ->sum('order');

                                    $totalRetailerCount = DB::table('tbl_retailer')
                                                    ->where('point_id',$users->point_id)
                                                    ->where('status',0)
                                                    ->count('point_id');

                                    if(sizeof($orders)>0){

                                        $totalQty  += $orders->total_qty;
                                        $totalValue += $orders->total_value;
                                    } 
                                       
                                    if(sizeof($totalDalivery)>0){
                                                
                                        $totalDelQty  += $totalDalivery->total_delivery_qty;
                                        $totalDelValue += $totalDalivery->total_delivery_value;
                                     }          
      
                                    $totalVisit +=$totalVistFo + $totalOrderFo;
                                    $totalOrder += $totalOrderFo;
                                    $totalRetailer += $totalRetailerCount;
                                    $routeTotalRetailer += $totalRouteRetailer;

                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                         <th>{{ $todate }}</th>
                                        <th>
                                            @if($users->business_type_id==1) {{'LIGHTING'}}
                                            @elseif($users->business_type_id==2) {{'ACCESSORISE'}}
                                            @else {{'FAN'}}
                                            @endif
                                        </th>
                                        <th>{{ $users->div_name }} </th>
                                       
                                        <th>{{ $users->point_name }}</th> 
                                                            
                                        <th>{{ $users->display_name }}</th>
                                        <th>{{ $users->email }}</th>                        
                                        <th>{{ $totalRetailerCount }}</th>
                                       <th>@if(sizeof($totalRouteName)>0){{$totalRouteName->rname}} @endif</th>
                                        <th>{{ $totalRouteRetailer }}</th>
                                        <th>{{$totalVistFo + $totalOrderFo}}</th>
                                        <th>{{$totalOrderFo}}</th>
                                           
                                        <th>@if(sizeof($orders->total_qty)>0){{ $orders->total_qty }} @else {{'0'}} @endif</th>                        
                                        <th>@if(sizeof($orders->total_qty)>0){{ number_format($orders->total_value,2) }} @else {{'0'}} @endif</th>
                                        <th>@if(sizeof($totalDalivery->total_delivery_qty)>0){{ $totalDalivery->total_delivery_qty }} @else {{'0'}} @endif</th>                        
                                        <th>@if(sizeof($totalDalivery->total_delivery_qty)>0){{ number_format($totalDalivery->total_delivery_value,2) }} @else {{ '0' }} @endif</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="7" style="text-align: right;">Grand Total : </th>
                                        <th>{{ $totalRetailer }}</th>
                                        <th></th>
                                        <th>{{$routeTotalRetailer}}</th>
                                        <th>{{ $totalVisit }}</th>
                                        <th>{{ $totalOrder }}</th>
                                        <th>{{ $totalQty }}</th>
                                        <th>{{ number_format($totalValue,2) }}</th>
                                        <th>{{ $totalDelQty }}</th>
                                        <th>{{ number_format($totalDelValue,2) }}</th>
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