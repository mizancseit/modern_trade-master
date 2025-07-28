@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DEPOT Transaction History 
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
						Depot Transaction History
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/DepotTransHistory') }}" method="get">
					
					<div class="row">
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
                        </div>

                        <div class="col-sm-5">
                            <select class="form-control show-tick" name="point_id" required="">
								<option value="">Please Select Depot</option>
									@foreach($depotList as $rowDepot)
										<option value="{{ $rowDepot->point_id }}">{{ $rowDepot->point_name }}</option>
									@endforeach
                            </select>

                        </div>
						
						 <div class="col-sm-2">
                           
							 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
                       
                    </div>  
				
				</form>	

						
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Depot Name</div>
									<div class="number"><?= (sizeof($depotPayment))?$depotPayment[0]->point_name:'';?></div>
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
									<div class="number"><?= (sizeof($depotPayment))?$depotPayment[0]->opening_balance:'0000.00';?> (TK)</div>
								</div>
							</div>
						</div>
                       
					   <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Depot Total Credited</div>
									<div class="number"><?= (isset($creditData[0]->total_credited))?$creditData[0]->total_credited:'0000.00' ?> (TK)</div>
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="info-box-4 hover-expand-effect">
								<div class="icon">
									<i class="material-icons col-teal"></i>
								</div>
								<div class="content">
									<div class="text">Market Total Sales</div>
									<div class="number"><?= (isset($debitData[0]->total_debited))?$debitData[0]->total_debited:'0000.00' ?> (TK)</div>
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
									if (sizeof($depotPayment) && sizeof($creditData) && sizeof($debitData) ) {
									echo $depotPayment[0]->opening_balance + ($creditData[0]->total_credited - $debitData[0]->total_debited);
									} else {
										echo '0000:00';
									}
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
						<th>Payment Type</th>
                        <th>Trasaction Type</th>
						<th>Credited</th>
						<th>Debited</th>
						<th>Balance (TK)</th>
                        
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotPayment) > 0)   
                    @php
                    $serial = 1;
                    $balance = $depotPayment[0]->opening_balance;
                    @endphp

                    @foreach($depotPayment as $RowDepotPayment) 
					
					<?php 
					   if($RowDepotPayment->transaction_type == 'credit')
						$balance += $RowDepotPayment->trans_amount;
					
					   if($RowDepotPayment->transaction_type == 'debit')
						$balance = $balance - $RowDepotPayment->trans_amount;
					   
					   
					?>
					
                    <tr>
                        <th>{{$serial}}</th>
                       
						<th>{{$RowDepotPayment->trans_date}}</th>
                        <th>{{$RowDepotPayment->payment_type}}</th>
                        <th>{{$RowDepotPayment->transaction_type}}</th>
						
						<?php if($RowDepotPayment->transaction_type == 'credit') { ?>
							<th>{{$RowDepotPayment->trans_amount}}</th>
						<?php } else { ?>
							<th>0000.00</th>
						<?php } ?>	
						  
						  
						<?php if($RowDepotPayment->transaction_type == 'debit') { ?>
						  <th>{{$RowDepotPayment->trans_amount}}</th>
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
						<th>Payment Type</th>
                        <th>Trasaction Type</th>
						<th>Credited</th>
						<th>Debited</th>
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
