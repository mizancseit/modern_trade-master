@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Depot Cashbook Summary
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
						Depot Cashbook Summary
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/DepotCashbookSummary') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromCashBookDate" id="fromdate" class="form-control" value="" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toCashBookDate" id="todate" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
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
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
						<th>Account Head</th>
						<th>Trans Type</th>
						<th>Trans Desc</th>
						<th>Credited</th>
						<th>Debited</th>
						<th>Balance</th>
	                    
                    </tr>
                </thead>
				
                 <tbody>
				 
				@if(sizeof($depotCashbookData) > 0) 

					<!--
					<div class="header">
						<h5>
							&nbsp;
							<div class="col-sm-12" align="right">
								<form action="{{url('DownloadDepotCashbookSummary')}}" enctype="multipart/form-data">
									<input type="submit" name="download" value="DOWNLOAD" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">
									<input type="hidden" name="fromCashBookDate" value="{{ $fromCashBookDate }}">
									<input type="hidden" name="toCashBookDate" value="{{ $toCashBookDate }}">
									
								</form>
							</div>	
						</h5>
					</div>
					-->
							
                    
					@php
                    $serial = 1;
					$OpeningBalance = 0;
					$GrandTotAmount = 0;
					$balance = 0;
                    @endphp

                    @foreach($depotCashbookData as $rowDepotCashbookData) 
					
					 <?php 
					 
					
					if($rowDepotCashbookData->perticular_head_id != 1)
						$balance -= $rowDepotCashbookData->trans_amount;
					else
						$balance += $rowDepotCashbookData->trans_amount;
						
					 ?>
					
                    <tr>
                        <th>{{$serial}}</th>
                       
						<th>{{$rowDepotCashbookData->accounts_head_name}}</th>
                	
						<th>{{$rowDepotCashbookData->trans_type}}</th>
						
						<th>{{$rowDepotCashbookData->trans_description}}</th>
						
						<?php if($rowDepotCashbookData->trans_type == 'credited') { ?>	
							<th>{{$rowDepotCashbookData->trans_amount}}</th>
						<?php } else {  ?>
							<th>0000</th>
						<?php } ?>					 
						
						 <?php if($rowDepotCashbookData->trans_type == 'debited') { ?>	
							<th>{{$rowDepotCashbookData->trans_amount}}</th>
						<?php } else {  ?>
							<th>0000</th>
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
						<th>Account Head</th>
						<th>Trans Type</th>
						<th>Trans Desc</th>
						<th>Credited</th>
						<th>Debited</th>
						<th>Balance</th>
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
