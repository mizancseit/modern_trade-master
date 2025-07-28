@extends('sales.masterPage')

@section('content')

<section class="content">
    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">

                <a href="JavaScript:void()" onclick="dashboardDistributorOrders(1)" title="Click To Details">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box-2 bg-teal hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">Total Requisition</div>
                                <div class="number">{{$resultOrderTotalRe}}</div>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="JavaScript:void()" onclick="dashboardDistributorOrders(2)" title="Click To Details">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    
                        <div class="info-box-2 bg-teal hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">Today Requisition</div>
                                <div class="number">{{$resultOrderPreviousRe}}</div>
                            </div>
                        </div>
                   
                </div>
                 </a>
                
                 <a href="JavaScript:void()" onclick="dashboardDistributorOrders(3)" title="Click To Details">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-red">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        <div class="content">
                            <div class="text">Delivery Pending</div>
                            <div class="number count-to" data-from="0" data-to="{{$resultOrderPending}}" data-speed="1000" data-fresh-interval="{{$resultOrderPending}}"></div>
                        </div>
                    </div>
                </div>
                </a>
                <a href="JavaScript:void()" onclick="dashboardDistributorOrders(4)" title="Click To Details">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-red">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Delivered</div>
                            <div class="number count-to" data-from="0" data-to="{{$resultOrderDelivery}}" data-speed="1000" data-fresh-interval="{{$resultOrderDelivery}}"></div>
                        </div>
                    </div>
                </div>
                </a>

                                
            </div>           

			<div class="row clearfix">

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">input</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Requisition</div>
                            <div class="number"><?php  if(isset($depoTodayReq[0]->totCount)) 
							{echo $depoTodayReq[0]->totCount;} else {echo 0;}  ?>   </div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">send</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Sent</div>
                            <div class="number"><?php  if (isset($depoTodaySend[0]->totCount)) 
								{echo $depoTodaySend[0]->totCount;} else {echo 0;} ?> </div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Acknoledge</div>
                            <div class="number"><?php if (isset($depoTodayAck[0]->totCount)) 
								
							{echo $depoTodayAck[0]->totCount;} else {echo 0;}  ?> </div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Approved</div>
                            <div class="number"><?php if (isset($depoTodayApprvd[0]->totCount)) 
								
							{echo $depoTodayApprvd[0]->totCount;} else {echo 0;}  ?> </div>
                        </div>
                    </div>
                </div>
				
				
				
			</div>	
			
			<div class="row clearfix">

				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">directions car</i>
                        </div>
                        <div class="content">
                            <div class="text">Today In-Transit</div>
                            <div class="number"><?php if (isset($depoTodayDelvrd[0]->totCount)) 
							{echo $depoTodayDelvrd[0]->totCount;} else {echo 0;} 	?> </div>
                        </div>
                    </div>
                </div>
			
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">assignment returned</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Received</div>
                            <div class="number"><?php if (isset($depoTodayRcvd[0]->totCount)) 
							{echo $depoTodayRcvd[0]->totCount;} else {echo 0;} 	?> </div>
                        </div>
                    </div>
                </div>
				
				
				
				 <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-red">
                        <div class="icon">
                            <i class="material-icons">delete forever</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Canceled</div>
                            <div class="number count-to" data-from="0" data-to="{{$depoTodayCancld[0]->totCount}}" data-speed="1000" data-fresh-interval="{{$depoTodayCancld[0]->totCount}}"></div>
                        </div>
                    </div>
                </div>
			
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">home</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Stock</div>
                            <div class="number"><?php if (isset($resStockSummary[0]->totStock)) 
								{echo $resStockSummary[0]->totStock;} else {echo 0;}  ?> </div>
			            </div>
                    </div>
                </div>
				
				<div class="row clearfix">
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-2 bg-teal hover-expand-effect">
							<div class="icon">
								<i class="material-icons">playlist_add_check</i>
							</div>
							<div class="content">
								<div class="text">Total Stock Value</div>
								<div class="number"><?php if (isset($resStockSummary[0]->totStockVal)) 
								{echo number_format($resStockSummary[0]->totStockVal,0);} else {echo 0;} 	?> </div>
							</div>
						</div>
					</div>	
				</div>		
				
			</div>		

			
    </div>
</section>

@endsection