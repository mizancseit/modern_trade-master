@extends('sales.masterPage') 
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            RETAILER Ledger 
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
						Retailer Ledger
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/PartyLaser') }}" method="get">
					
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
                            <select class="form-control show-tick" name="route_id" data-live-search="true" required="" onchange="getRetailerList(this.value)">
								<option value="">Please Select Route</option>
									@foreach($routeList as $rowRoute)
										<option value="{{ $rowRoute->route_id }}">{{ $rowRoute->rname }}</option>
									@endforeach
                            </select>
						</div>

                        <div class="col-sm-5" id="div_ratailer">
                            <select class="form-control show-tick" name="ratailer_id" data-live-search="true" required="">
								<option value="">Please Select Retailer</option>
				            </select>
                        </div>
						
						 <div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
                       
                    </div>  
				
				</form>	

						
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text"></div>
									<div class="number"><?= (sizeof($retLaserData))?$retLaserData[0]->RetName:'';?></div>
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
									<div class="number"><?= (isset($retOpeningBalance[0]->opening_balance))?$retOpeningBalance[0]->opening_balance:'0000.00' ?> (TK)</div>
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
									<?php 
									
									$totSales = 0;
									if( sizeof($retGrandTotData)>0 && $retGrandTotData[0]->totSales > 0)
									{
										$totSales = $retGrandTotData[0]->totSales;
									}
									
									if( sizeof($retGrandTotData) > 0 && $retGrandTotData[0]->totSalesReturn > 0)
									{
										$totSales = $totSales - $retGrandTotData[0]->totSalesReturn;
									}


										
									
									?>
									
									
									<div class="number"><?= (isset($totSales))?($totSales):'0000.00' ?> (TK)</div>
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
									<div class="number"><?= (isset($retGrandTotData[0]->totCollection))?$retGrandTotData[0]->totCollection:'0000.00' ?> (TK)</div>
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
									if( isset($retOpeningBalance[0])):
									 echo ($retOpeningBalance[0]->opening_balance + ($totSales - $retCommisionVal[0]->tot_memo_com) ) -  $retGrandTotData[0]->totCollection;
								    else:
									 echo '0000.00';
									endif;		
									
									
									?> (TK)</div>
								</div>
							</div>
						</div>
                       

                       
                    </div>                                  
               
         
          
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
						<th>Transaction Date</th>
						<th>Collection</th>
						<th>Sales</th>
						<th>Return Sales</th>
						<th>Memo Commission</th>
						<th>Balance (TK)</th>
                        
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($retLaserData) > 0)   
                    @php
                    $serial = 1;
                    $balance = $retOpeningBalance[0]->opening_balance;
                    @endphp

                    @foreach($retLaserData as $RowRetLasrData) 
					
					<?php 
					   if($RowRetLasrData->retailer_collection > 0)
						$balance -= $RowRetLasrData->retailer_collection;
					
					   if($RowRetLasrData->retailer_invoice_sales != 0)
						$balance += $RowRetLasrData->retailer_invoice_sales;
					
					   if($RowRetLasrData->retailer_sales_return > 0)
						$balance -= $RowRetLasrData->retailer_sales_return;
					
					  if($RowRetLasrData->memo_commission_value > 0)
						$balance -= $RowRetLasrData->memo_commission_value;
					   
					   
					?>
					
                    <tr>
                        <th>{{$serial}}</th>
                       
						<th>{{$RowRetLasrData->credit_ledger_date}}</th>
                     
						<?php if($RowRetLasrData->retailer_collection > 0) { ?>
							<th>{{$RowRetLasrData->retailer_collection}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>	
						  
						  
						<?php if($RowRetLasrData->retailer_invoice_sales != 0) { ?>
						  <th>{{$RowRetLasrData->retailer_invoice_sales}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>
						
						
						<?php if($RowRetLasrData->retailer_sales_return > 0) { ?>
						  <th>{{$RowRetLasrData->retailer_sales_return}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>
						

						<?php if($RowRetLasrData->memo_commission_value > 0) { ?>
						  <th>{{$RowRetLasrData->memo_commission_value}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>	
						 
						<th>{{$balance}}</th>
                       
                    </tr>
					
					
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
						<th>Transaction Date</th>
						<th>Collection</th>
						<th>Sales</th>
						<th>Return Sales</th>
						<th>Memo Commission</th>
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
