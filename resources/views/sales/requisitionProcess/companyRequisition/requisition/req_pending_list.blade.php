@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        ACKNOWLEDGE REQUISITION
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Acknowledge Requisition 
                        </small>
                    </h2>
                </div>
                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>ACKNOWLEDGE REQUISITION PENDING LIST</h2>   
								
                </div>
                
                <div class="body">
				    
				<form action="{{ URL('/dist/reqAcknowledge') }}" method="POST">	
				 {{ csrf_field() }}
				
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Distributor</th>
								<th>Point</th>
                                <th>Req No</th>
                                <th>Req Date</th>
                                <th>Balance</th>
                                <th>Qty</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Acknowledge</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultReqList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

							<?php $dynLabel = 1; ?>
							
                            @foreach($resultReqList as $ReqRow)
                                           
                            <tr>
                                <th>{{ $serial }}</th>
								<th>
                                    <a href="{{ URL('/dist/reqDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                       {{ $ReqRow->display_name }}
                                    </a>
                                </th>
								
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ ($ReqRow->distributor_current_balance!=0)?$ReqRow->distributor_current_balance:0 }}</th>
                                <th>{{ $ReqRow->totQnty }}</th>
                                <th>{{ $ReqRow->totVal }}</th>
								<th>{{ $ReqRow->req_status }}</th>	

								<th>
								
								<div class="demo-radio-button">
                        			
									<input name="ordack<?=$ReqRow->req_id?>" type="radio" id="radio_yes<?=$dynLabel?>" class="radio-col-red" value="YES">
									<label for="radio_yes<?=$dynLabel?>"> YES </label>
									
									<input name="ordack<?=$ReqRow->req_id?>" type="radio" id="radio_no<?=$dynLabel?>" class="radio-col-red" value="NO">
									<label for="radio_no<?=$dynLabel?>"> NO </label>
									
									
									<input type="hidden" name="reqid[]" value="<?=$ReqRow->req_id?>"  />
									
                                </div>								   
								</th>								
                            </tr>
							
							
							
                            @php
                            $serial++;
							$dynLabel++;
						    @endphp
                            @endforeach

                        @else
                            <tr>
                                <th colspan="10">No record found.</th>
                            </tr>
                        @endif    
						
								<tr>
									<th colspan="10" >
										<div class="col-sm-12" align="center">
											<input  type="submit" name="ORDER_ACKNOWLEDGE" value="ACKNOWLEDGE" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
										</div>	
									</th>
								</tr>	
                            
                        </tbody>
                    </table>    
				</form>	
              </div>

            </div>
        </div>
    </div>
</section>

@endsection