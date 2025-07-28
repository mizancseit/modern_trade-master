@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION IN Active List
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition IN Active List 
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
                    <h2>REQUISITION INACTIVE LIST</h2>   
								
                </div>
				
				
                
                <div class="body">
				
				<form action="{{ URL('/reqAllInActiveList') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromReqDate" id="fromdate1" class="form-control" value="" placeholder="from Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toReqDate" id="todate1" class="form-control" value="" placeholder="to Date">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-2">
                         			
											<select id="div_id" name="div_id" class="form-control show-tick">
													<option value="">Select Division</option>
												<?php foreach($divList as $rowDiv) { ?>
													<?php if(sizeof($resultReqList) && $resultReqList[0]->div_id == $rowDiv->div_id) { ?>
													 <option value="<?= $rowDiv->div_id ?>" selected><?= $rowDiv->div_name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowDiv->div_id ?>"><?= $rowDiv->div_name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="business_type" name="business_type">
								<option value="" selected>LA</option>
								<option value="1" <?=(sizeof($resultReqList) && $resultReqList[0]->business_type_id == 1)?'selected':''; ?> >LIGHITING</option>
								<option value="2" <?=(sizeof($resultReqList) && $resultReqList[0]->business_type_id == 2)?'selected':''; ?>>ACCESSORIES</option>
							</select>
						</div>
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="req_status" name="req_status">
								<option value="">Type</option>
								<option value="">ALL</option>
								<option value="acknowledge" <?=(sizeof($resultReqList) && $resultReqList[0]->req_status == 'acknowledge')?'selected':''; ?>>ACK</option>
								<option value="send" <?=(sizeof($resultReqList) && $resultReqList[0]->req_status == 'send')?'selected':''; ?>>NOT ACK</option>
							</select>
						</div>
						
							<div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
						
					</div>

					
				
				</form>	
				
				
				    
				<form action="{{ URL('/reqActiveProcess') }}" method="POST">	
				 {{ csrf_field() }}
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Depot</th>
								<th>Point</th>
                                <th>Req No</th>
                                <th>Req Date</th>
                                <th>Qnty</th>
                                <th>Value</th>
                                <th>Ava Bal</th>
                                <th>Credit Limit</th>
                                <th>Used Amount</th>
                                <th>Active</th>
							    
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
							
							
							<?php  
							
							$depo_ava_bal = 0;
							$credit_limit = 0;
							$total_debited = 0;
							
							$depot_bal = DB::select("SELECT credit_limit FROM tbl_distributor_balance WHERE distributor_id 
								in (SELECT u.id FROM users u JOIN tbl_user_business_scope ubs ON u.id = ubs.user_id
								WHERE ubs.point_id = '".$ReqRow->point_id."' and ubs.is_active = 0
								AND u.user_type_id = 5)");
								
							if(sizeof($depot_bal)>0)
							{
							  $credit_limit = $depot_bal[0]->credit_limit;
							}		
								
							$depot_cred = DB::select("SELECT SUM(trans_amount) as total_credited FROM depot_accounts_payments 
							WHERE point_id = '".$ReqRow->point_id."' AND transaction_type = 'credit' AND ack_status='CONFIRMED' AND
							confirmed_date !=''");
							
							$depot_deb = DB::select("SELECT SUM(trans_amount) as total_debited FROM depot_accounts_payments 
							WHERE point_id = '".$ReqRow->point_id."' AND transaction_type = 'debit'");
							
							if(sizeof($depot_deb)>0)
							{
								$total_debited = $depot_deb[0]->total_debited;
							}
							
							$depo_ava_bal = $depot_cred[0]->total_credited - $total_debited;
	
							?>
							
							
                                           
                            <tr>
                                <th>{{ $serial }}</th>
                            	
								
								<th>
                                    <a href="{{ URL('/reqDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                       {{ $ReqRow->display_name }}
                                    </a>
                                </th>
								
								
								
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $ReqRow->totQnty }}</th>
                                <th>{{ $ReqRow->totVal }}</th>
                                <th>{{ $depo_ava_bal }}</th>
                                <th>{{ $credit_limit }}</th>
                                <th>{{ $total_debited }}</th>
							
								<th>
								
								<div class="demo-radio-button">
                        			
									<input name="ordack<?=$ReqRow->req_id?>" type="radio" id="radio_yes<?=$dynLabel?>" class="radio-col-red" value="YES">
									<label for="radio_yes<?=$dynLabel?>"> YES </label>
									
									<input name="ordack<?=$ReqRow->req_id?>" type="radio" id="radio_no<?=$dynLabel?>" class="radio-col-red" value="NO">
									<label for="radio_no<?=$dynLabel?>"> NO </label>
									
									
									<input type="hidden" name="reqid[]" value="<?=$ReqRow->req_id?>"  />
									
									<input type="hidden" name="point_id<?=$ReqRow->req_id?>" value="<?=$ReqRow->point_id?>"  />
									<input type="hidden" name="depot_in_charge<?=$ReqRow->req_id?>" value="<?=$ReqRow->depot_in_charge?>"  />
									<input type="hidden" name="trans_amount<?=$ReqRow->req_id?>" value="<?=$ReqRow->totVal?>"  />
									
                                </div>
								
								   
								</th>
								
								
								
							
							<!--
								<th>
                                    <a href="{{ URL('/reqApproved/'.$ReqRow->req_id) }}" title="Click To Approved" onClick="return confirm('Are you sure to Approved?')">
                                        Approved
                                    </a>  ||  
									
									 <a href="{{ URL('/reqCanceled/'.$ReqRow->req_id) }}" title="Click To Canceled" onClick="return confirm('Are you sure to Cancele?')">
                                        Canceled
                                    </a>
                                </th>	
							-->		
                        		
                               
								
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
											<input  type="submit" name="ORDER_ACTIVE" value="ORDER ACTIVE" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
										</div>	
									</th>
								</tr>	
                            
                        </tbody>
                    </table>
                    </div>   
				</form>	
              </div>

            </div>
        </div>
    </div>
</section>

@endsection