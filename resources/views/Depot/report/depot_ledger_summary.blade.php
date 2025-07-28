@extends('sales.masterPage') 
@section('content')
 <section class="content">
        <div class="container-fluid">
	
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Party Ledger Summary
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Party
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
						Party Ledger Summary
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/DepotLedger') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ $fromdate }}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ $todate}}" placeholder="Select To Date"  readonly="">
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
           <!-- <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable"> -->
		   <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Sl</th>
						<th>Party Name</th>
						<th>Ledger Date</th>
						<th>Opening Balance</th>
						<th>Lifting</th>
						<th>Collection</th>
						<th>Closing Balance</th>
						<th>SAP Closing Balance</th>
						<th>Adjustment</th>
						<th>Actual Closing Balance</th>
                    </tr>
                </thead>
				
                 <tbody>
				 
				@if(sizeof($DepotLedgerData) > 0)

					<div class="header">
						<h5>
							&nbsp;
							<!-- 
							<div class="col-sm-12" align="right">
								<form action="{{url('DownloadDeopotLedger')}}" enctype="multipart/form-data">
									<input type="submit" name="download" value="DOWNLOAD" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">
								</form>
							</div>	
							-->
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

					
                    @foreach($DepotLedgerData as $rowDepotLedgerData) 
					 
			        <tr>
					
                        <th>{{$serial}}</th>
                       
						<th>{{$rowDepotLedgerData->display_name  . ' ('. $rowDepotLedgerData->sap_code  .')' }}</th>
						
						<th>{{date('Y-m-d',strtotime($rowDepotLedgerData->ledger_date_time))}}</th>
                     
					
						<th>
							{{ $rowDepotLedgerData->party_opening_balance }}
						</th>
						
						<th>
							{{ $rowDepotLedgerData->party_lifting_total }}
						</th>
					
					
						<th>
							{{ $rowDepotLedgerData->party_collection_total }}
						</th>
						
						<th>
							{{ $rowDepotLedgerData->closing_balance_total }}
						</th>
						
						<th>
							{{ $rowDepotLedgerData->sap_closing_balance }}
						</th>
						
						<th>
							{{ $rowDepotLedgerData->party_adjustment }}
						</th>
						
						
						
						<th>
							{{ $rowDepotLedgerData->actual_closing_balance }}
						</th>
						
                       
                    </tr>
					
					@endforeach  
					
                @else
                    <tr>
                        <th colspan="10">No record found.</th>
                    </tr>
                @endif   
				 
             
			  
               
                </tbody>
				
                <tfoot>
                  
				   <th>Sl</th>
						<th>Party Name</th>
						<th>Ledger Date</th>
						<th>Opening Balance</th>
						<th>Lifting</th>
						<th>Collection</th>
						<th>Closing Balance</th>
						<th>SAP Closing Balance</th>
						<th>Adjustment</th>
						<th>Actual Closing Balance</th>
				  
				  
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
