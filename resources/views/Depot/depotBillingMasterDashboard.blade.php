@extends('sales.masterPage')

@section('content')

<section class="content">
    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
             


			<div class="row clearfix">

                <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Requisition</div>
                            <div class="number"><?php if (isset($depoTodaySend[0]->totCount)) 
							{echo $depoTodaySend[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Acknowledge</div>
                            <div class="number"><?php if (isset($depoTodayAck[0]->totCount)) 
							{echo $depoTodayAck[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Analysis</div>
                            <div class="number"><?php if (isset($depoTodayApprvd[0]->totCount)) 
							{echo $depoTodayApprvd[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				
			</div>	
			
			
			<div class="row clearfix">
			
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Sales Order </div>
                            <div class="number"><?php if (isset($depoTodayDownload[0]->totCount)) 
							{echo $depoTodayDownload[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today In-Transit </div>
                            <div class="number"><?php if (isset($depoTodayDlvrd[0]->totCount)) 
							{echo $depoTodayDlvrd[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Received</div>
                            <div class="number"><?php if (isset($depoTodayRcvd[0]->totCount)) 
							{echo $depoTodayRcvd[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				
				
			
			</div>	

			<div class="row clearfix">
			
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today Canceled</div>
                            <div class="number"><?php if (isset($depoTodayCnled[0]->totCount)) 
							{echo $depoTodayCnled[0]->totCount; } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
			
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Stock</div>
                            <div class="number"><?php if (isset($depoTotalStock[0]->totStock)) 
							{echo number_format($depoTotalStock[0]->totStock,0); } else {echo 0;}?></div>
                        </div>
                    </div>
                </div>
				


			</div>				
			
			
			
    </div>
</section>

@endsection