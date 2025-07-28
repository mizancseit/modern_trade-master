@extends('sales.masterPage') 
@section('content')
 <section class="content">
        <div class="container-fluid">
		
		

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            RETAILER Credit Summary
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Point
                            </small>
                        </h2>
                    </div>
                  
                </div>
                
            </div>

             @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
           

            <div class="row clearfix">
               
            
            <!-- #END# Exportable Table -->
			
			
			
      
           <div class="card">
				<div class="header">
					<h2>
						Retailer Credit Summary
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/RetailerCreditSummary') }}" method="get">
					
					<div class="row">
					
					   <!--
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div> -->
						
						<div class="col-sm-5">
                            <select class="form-control show-tick" name="route_id" required="" onchange="">
								<option value="">Please Select Route</option>
								<option value="all">All</option>
									@foreach($routeList as $rowRoute)
										<option value="{{ $rowRoute->route_id }}">{{ $rowRoute->rname }}</option>
									@endforeach
                            </select>
						</div>
 
						
						 <div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
                       
                    </div>  
				
				</form>	

					<!--	
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text"></div>
									<div class="number"><?//= (sizeof($retLaserData))?$retLaserData[0]->RetName:'';?></div>
								</div>
							</div>
						</div>
					</div>	
				 
                    <div class="row">
						
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Opening Balance</div>
									<div class="number"><?//= (isset($retOpeningBalance[0]->opening_balance))?$retOpeningBalance[0]->opening_balance:'0000.00' ?> (TK)</div>
								</div>
							</div>
						</div>
                       
					   <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Total Collection</div>
									<div class="number"><?//= (isset($retGrandTotData[0]->totCollection))?$retGrandTotData[0]->totCollection:'0000.00' ?> (TK)</div>
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Total Sales</div>
									<div class="number"><?//= (isset($retGrandTotData[0]->totSales))?$retGrandTotData[0]->totSales:'0000.00' ?> (TK)</div>
								</div>
							</div>
						</div>
						
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Current Balance</div>
									<div class="number"><?php
								/*
									if( isset($retOpeningBalance[0])):
									 echo ($retOpeningBalance[0]->opening_balance + $retGrandTotData[0]->totSales) -  $retGrandTotData[0]->totCollection;
								    else:
									 echo '0000.00';
									endif;	
								*/									
									
									
									?> (TK)</div>
								</div>
							</div>
						</div>
                       

                       
                    </div>   -->                               
               
         
          
    
        <div class="table-responsive">
           <!-- <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable"> -->
		   <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Sl</th>
						<th>Retailer Name</th>
						<th>Opening Balance</th>
						<th>Sales(All Kinds of Sales)</th>
						<th>Collection</th>
						
						<th>Balance (TK)</th>
                        
                    </tr>
                </thead>
				
                 <tbody>
				 
				@if(sizeof($retailerData) > 0)

					<div class="header">
						<h5>
							&nbsp;
							<div class="col-sm-12" align="right">
								<form action="{{url('DownloadCreditSummary')}}" enctype="multipart/form-data">
									<input type="submit" name="download" value="DOWNLOAD" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">
									<input type="hidden" name="route_id" value="{{ $route_id }}">
								</form>
							</div>	
						</h5>
					</div>
					
                    
					@php
                    $serial = 1;
					$GrandtotSales = 0;
					$GrandtotCollection = 0;
					$GarndOpeningBalance = 0;
					$GrandtotBalance = 0;
					$balance = 0;
                    @endphp

					
					
                    @foreach($retailerData as $rowRetailerData) 
					
					 <?php
					 
					 /*
					$rowCreditSummaryData = DB::select("
											SELECT SUM(rled.retailer_invoice_sales) totSales, 
												   SUM(rled.retailer_collection) totCollection, 
												   SUM(rled.retailer_sales_return) totSalesReturn
												FROM  retailer_credit_ledger rled 
												WHERE  rled.point_id in (SELECT point_id FROM tbl_user_business_scope 
																		WHERE user_id = '".Auth::user()->id."') 
												and rled.retailer_id = '".$rowRetailerData->retailer_id."'
											");  
					*/			

					/*
					$rowCreditSummaryData = DB::select("
											SELECT SUM(rled.retailer_invoice_sales) totSales, 
												   SUM(rled.retailer_collection) totCollection 
												FROM  retailer_credit_ledger rled 
												WHERE  rled.point_id in (SELECT point_id FROM tbl_user_business_scope 
																		WHERE user_id = '".Auth::user()->id."') 
												and rled.retailer_id = '".$rowRetailerData->retailer_id."'
											"); 
					*/		


					$rowCreditSummaryData = DB::select("SELECT SUM(retailer_invoice_sales) ret_wise_tot_sales, 
											SUM(retailer_sales_return) ret_wise_sales_return,
											SUM(retailer_collection) totCollection 
											FROM  retailer_credit_ledger 
										WHERE retailer_id = '".$rowRetailerData->retailer_id."'
										AND point_id = '".$rowRetailerData->point_id."'");	 		
					
					 
					
						$depoMarketCredit = 0;
						if(sizeof($retOpenTot)>0)
						{
							$totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
						} else {
							$totRetOpenBal = '0000';
						}
						
						
						if(sizeof($rowCreditSummaryData)>0)
						{
							$GrandtotSales += $rowCreditSummaryData[0]->ret_wise_tot_sales;
							
							if($rowCreditSummaryData[0]->ret_wise_sales_return>0)
							{
								$GrandtotSales -= $rowCreditSummaryData[0]->ret_wise_sales_return;
							}
						} 
						
						
						
						/*
						if(sizeof($depoTotRetSales)>0)
						{
							$tot_return_sales = $depoTotRetSales[0]->totSalesReturn;
						} else {
							$tot_return_sales = '0000';
						}
						*/
						
						
						if(sizeof($rowCreditSummaryData)>0)
						{
							$GrandtotCollection += $rowCreditSummaryData[0]->totCollection;
						} 	
						
						
					
					 
					 
					 ?>
					 
					 
					
					
                    <tr>
                        <th>{{$serial}}</th>
                       
						<th>{{$rowRetailerData->name}}</th>
                     
					
							<th>
								
								{{ $rowRetailerData->opening_balance }}
							
							</th>
					
						  
						  
						<?php if($rowCreditSummaryData[0]->ret_wise_tot_sales > 0) { ?>
						  <th>{{$rowCreditSummaryData[0]->ret_wise_tot_sales - $rowCreditSummaryData[0]->ret_wise_sales_return}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>	
						
						<?php if($rowCreditSummaryData[0]->totCollection > 0) { ?>
						  <th>{{$rowCreditSummaryData[0]->totCollection}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>

									
						 
						
						<th>{{ ($rowRetailerData->opening_balance + ($rowCreditSummaryData[0]->ret_wise_tot_sales - $rowCreditSummaryData[0]->ret_wise_sales_return)) - $rowCreditSummaryData[0]->totCollection }}</th> 
                       
                    </tr>
					
					
                   
                
					@php
						$serial++;
						$depoMarketCredit += ($totRetOpenBal + $GrandtotSales) - $GrandtotCollection;		
                    @endphp
                    @endforeach
					
					
					
					<tr>
					     <th>&nbsp;</th>
					     <th>Total</th>
					     <th>{{ number_format($totRetOpenBal,0) }}</th>
					     <th>{{ number_format($GrandtotSales,0) }}</th>
					     <th>{{ number_format($GrandtotCollection,0) }}</th>
					     <th>{{ number_format($depoMarketCredit,0) }}</th>
					</tr>
					
					
					
					
                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif   
				 
             
			  
               
                </tbody>
				
                <tfoot>
                   <tr>
						<th>Sl</th>
						<th>Retailer Name</th>
						<th>Opening Balance</th>
						<th>Sales</th>
						<th>Collection</th>
						
						<th>Balance (TK)</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
