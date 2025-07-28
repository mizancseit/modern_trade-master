@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Depot Expense Summary
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
						Depot Expense Summary
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/DepotExpenseSummary') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromExpenseDate" id="fromdate" class="form-control" value="" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toExpenseDate" id="todate" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-5">
                            <select class="form-control show-tick" name="accounts_head_id" required="" onchange="">
								<option value="">Please Select Expense</option>
								<option value="all">All Expense</option>
									@foreach($expenseList as $rowExpense)
										<option value="{{ $rowExpense->accounts_head_id }}">{{ $rowExpense->accounts_head_name }}</option>
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
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
						<th>Depot Name</th>
						<th>Account Head</th>
						<th>Trans Type</th>
						<th>Amount</th>
	                    
                    </tr>
                </thead>
				
                 <tbody>
				 
				@if(sizeof($depotExpenseData) > 0) 

					<div class="header">
						<h5>
							&nbsp;
							<div class="col-sm-12" align="right">
								<form action="{{url('DownloadExpenseSummary')}}" enctype="multipart/form-data">
									<input type="submit" name="download" value="DOWNLOAD" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">
									<input type="hidden" name="fromExpenseDate" value="{{ $fromExpenseDate }}">
									<input type="hidden" name="toExpenseDate" value="{{ $toExpenseDate }}">
									<input type="hidden" name="accounts_head_id" value="{{ $accounts_head_id }}">
								</form>
							</div>	
						</h5>
					</div>
							
                    
					@php
                    $serial = 1;
					$OpeningBalance = 0;
					$GrandTotAmount = 0;
					$balance = 0;
                    @endphp

                    @foreach($depotExpenseData as $rowDepotExpenseData) 
					
					 <?php $OpeningBalance = 0; 
					 
					 $GrandTotAmount += $rowDepotExpenseData->amount;
					 ?>
					
                    <tr>
                        <th>{{$serial}}</th>
                       
						<th>{{$rowDepotExpenseData->point_name}}</th>
                     
					
						<th>{{$rowDepotExpenseData->accounts_head_name}}</th>
						
						<th>{{$rowDepotExpenseData->trans_type}}</th>
						 
						<th>{{$rowDepotExpenseData->amount}}</th>
                       
                    </tr>
					
					
                   
                
					@php
						$serial++;
                    @endphp
                    @endforeach
					
					<tr>
                        <th colspan="4">&nbsp;</th>
                        <th>{{$GrandTotAmount}}</th>
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
						<th>Depot Name</th>
						<th>Account Head</th>
						<th>Trans Type</th>
						<th>Amount</th>
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
