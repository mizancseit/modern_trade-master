@extends('sales.masterPage')
@section('content') 
<section class="content" id="contentReplace">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PAYMENT VERIFY
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> /  Verify
                        </small>
                    </h2>
                </div>
				<div class="col-lg-3" style="text-align: right;">
                    <h2>                        
                        <small> 
                            <a href="JavaScript:void()" onclick="window.history.go(-1); return false;"> << BACK PREVIEW PAGE </a> | 
                            <a href="JavaScript:void()" onclick="activeMeu()" id="onlyMenu"> MENU SHOW </a>
                            <input type="hidden" id="onlyMenuValue" value="0">
                        </small>
                    </h2>
                </div>
                </div>
            </div>
        </div>
			@if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card" style="overflow-y: auto;">
                
				<div class="header">
                   <h2>PAYMENT ACKNOWLEDGE LIST</h2>  
								
                </div>
				
                <div class="body">
				
				<form action="{{ URL('/depotPaymentAckList') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromPaymentDate" id="fromdate1" class="form-control" value="<?= $sel_fromPaymentDat; ?>" autocomplete="off" placeholder="Acknowledge from Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toPaymentDate" id="todate1" class="form-control" value="<?= $sel_toPaymentDate; ?>" autocomplete="off" placeholder="Acknowledge to Date">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-4">
                            <div class="input-group">
							
                               <?php  
									$ssg_user = DB::select("select * from users where user_type_id = 5"); ?>
								 
									<select id="sap_code" name="sap_code" class="form-control show-tick">    

										<option value="">SAP Code</option>	
										
										<?php foreach( $ssg_user as $rowUser){ 
										    
											if ($sel_sap_code == $rowUser->sap_code ) { ?>
											
										  <option value="<?php echo $rowUser->sap_code; ?>" selected><?php  echo $rowUser->sap_code."::".$rowUser->display_name; ?></option>
										
											<?php } else {	?>
										
										<option value="<?php echo $rowUser->sap_code; ?>"><?php  echo $rowUser->sap_code."::".$rowUser->display_name; ?></option>
										
										<?php } 
										
										
										} ?>
										
										
										   
									</select>	
                            
							</div>
                        </div>
						
						
						<div class="col-sm-4">
                         			
									<!--
											<select id="bank_name" name="bank_name" class="form-control show-tick">
													<option value="">Select Bank</option>
													<option value="AB Bank Limited">AB Bank Limited</option>
													<option value="Agrani Bank Limited">Agrani Bank Limited</option>
													<option value="Al-Arafah Islami Bank Limited">Al-Arafah Islami Bank Limited</option>
													<option value="Bangladesh Commerce Bank Limited">Bangladesh Commerce Bank Limited</option>
													<option value="Bangladesh Development Bank Limited">Bangladesh Development Bank Limited</option>
													<option value="Bangladesh Krishi Bank">Bangladesh Krishi Bank</option>
													<option value="Bank Al-Falah Limited">Bank Al-Falah Limited</option>
													<option value="Bank Asia Limited">Bank Asia Limited</option>
													<option value="BASIC Bank Limited">BASIC Bank Limited</option>
													<option value="BRAC Bank Limited">BRAC Bank Limited</option>
													<option value="Citibank N.A">Citibank N.A</option>
													<option value="Commercial Bank of Ceylon Limited">Commercial Bank of Ceylon Limited</option>
													<option value="Dhaka Bank Limited">Dhaka Bank Limited</option>
													<option value="Dutch-Bangla Bank Limited">Dutch-Bangla Bank Limited</option>
													<option value="Eastern Bank Limited">Eastern Bank Limited</option>
													<option value="EXIM Bank Limited">EXIM Bank Limited</option>
													<option value="First Security Islami Bank Limited">First Security Islami Bank Limited</option>
													<option value="Habib Bank Ltd.">Habib Bank Ltd.</option>
													<option value="ICB Islamic Bank Ltd.">ICB Islamic Bank Ltd.</option>
													<option value="IFIC Bank Limited">IFIC Bank Limited</option>
													<option value="Islami Bank Bangladesh Ltd">Islami Bank Bangladesh Ltd</option>
													<option value="Jamuna Bank Ltd">Jamuna Bank Ltd</option>
													<option value="Janata Bank Limited">Janata Bank Limited</option>
													<option value="Meghna Bank Limited">Meghna Bank Limited</option>
													<option value="Mercantile Bank Limited">Mercantile Bank Limited</option>
													<option value="Midland Bank Limited">Midland Bank Limited</option>
													<option value="Mutual Trust Bank Limited">Mutual Trust Bank Limited</option>
													<option value="National Bank Limited">National Bank Limited</option>
													<option value="National Bank of Pakistan">National Bank of Pakistan</option>
													<option value="National Credit & Commerce Bank Ltd">National Credit & Commerce Bank Ltd</option>
													<option value="NRB Commercial Bank Limited">NRB Commercial Bank Limited</option>
													<option value="One Bank Limited">One Bank Limited</option>
													<option value="Premier Bank Limited">Premier Bank Limited</option>
													<option value="Prime Bank Ltd">Prime Bank Ltd</option>
													<option value="Pubali Bank Limited">Pubali Bank Limited</option>
													<option value="Rajshahi Krishi Unnayan Bank">Rajshahi Krishi Unnayan Bank</option>
													<option value="Rupali Bank Limited">Rupali Bank Limited</option>
													<option value="Shahjalal Bank Limited">Shahjalal Bank Limited</option>
													<option value="Shimanto Bank Limited">Shimanto Bank Limited</option>
													<option value="Social Islami Bank Ltd.">Social Islami Bank Ltd.</option>
													<option value="Sonali Bank Limited">Sonali Bank Limited</option>
													<option value="South Bangla Agriculture & Commerce Bank Limited">South Bangla Agriculture & Commerce Bank Limited</option>
													<option value="Southeast Bank Limited">Southeast Bank Limited</option>
													<option value="Standard Bank Limited">Standard Bank Limited</option>
													<option value="Standard Chartered Bank">Standard Chartered Bank</option>
													<option value="State Bank of India">State Bank of India</option>
													<option value="The City Bank Ltd.">The City Bank Ltd.</option>
													<option value="The Hong Kong and Shanghai Banking Corporation. Ltd.">The Hong Kong and Shanghai Banking Corporation. Ltd.</option>
													<option value="Trust Bank Limited">Trust Bank Limited</option>
													<option value="Union Bank Limited">Union Bank Limited</option>
													<option value="United Commercial Bank Limited">United Commercial Bank Limited</option>
													<option value="Uttara Bank Limited">Uttara Bank Limited</option>
													<option value="Woori Bank">Woori Bank</option>
												</select>
										-->		
												
									<?php  
											$ssg_bank=DB::select("select * from tbl_master_bank where business_type in (1,2) and status=0 order by id asc"); ?>
                                          
                                         
                                            <select id="bank_name" name="ssgbank_name" class="form-control show-tick">     
												<option value="">Bank Name</option>												
												<?php foreach( $ssg_bank as $bank){
													if($sel_ssgbank_name == $bank->id) {
													?>
												<option value="<?php echo $bank->id ?>" selected><?php  echo $bank->acctshortname."::".$bank->shortcode."::".$bank->bank_name; ?> </option>
													<?php  } else { ?>
												<option value="<?php echo $bank->id ?>"><?php  echo $bank->acctshortname."::".$bank->shortcode."::".$bank->bank_name; ?> </option>	
													
											<?php } 
											
											} ?>
												
                                                   
											</select>		

											
						</div>
						
					</div>
						
					<div class="row">	
						
							<div class="col-sm-3">
							   <select class="form-control show-tick" id="payment_type" name="payment_type">
									<option value="">Payment Method</option>
									<option value="CASH" <?= ($sel_payment_type == 'CASH')?'selected':''; ?>>CASH</option>
									<option value="CHEQUE" <?= ($sel_payment_type == 'CHEQUE')?'selected':''; ?>>CHEQUE</option>
									<option value="ON-LINE" <?= ($sel_payment_type == 'ON-LINE')?'selected':''; ?>>ON-LINE</option>
									<option value="PAY-ORDER" <?= ($sel_payment_type == 'PAY-ORDER')?'selected':''; ?>>PAY-ORDER</option>
									<option value="DD" <?= ($sel_payment_type == 'DD')?'selected':''; ?>>DD</option>
									<option value="TT" <?= ($sel_payment_type == 'TT')?'selected':''; ?>>TT</option>
								</select>
							</div>
							
							<div class="col-sm-2">
								   <select class="form-control show-tick" id="business_type" name="business_type">
										<option value="">LA</option>
										<option value="1" <?= ($sel_business_type == '1')?'selected':''; ?>>LIGHITING</option>
										<option value="2" <?= ($sel_business_type == '2')?'selected':''; ?>>ACCESSORIES</option>
									</select>
							</div>
							
							<div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
							</div>
						
							
						
					</div>

					
				
				</form>	
				
				    
				<form action="{{ URL('/depotPaymentVerify') }}" method="POST">	
				 {{ csrf_field() }}

                  @if(sizeof($paymentList) > 0)   
                    @php
					 $serial =1;
                     $dynLabel = 1; 
                     $sess_id=Auth::user()->id;
                     $user_id=DB::select("select * from users where id=$sess_id");
                    @endphp

                 <div class="table-responsive">  
				
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
					
					
                        <thead>
									<th colspan="13" >
                                        <div class="col-sm-12" align="right">
                                            <input  type="submit" name="ORDER_VERIFY" value="VERIFY" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
                                        </div>  
                                    </th>
									
                            <tr style="font-size: 11px; font-weight: normal;">

                                <th>Date</th>
								<th>Sap Code</th>
								<th>Division</th>
								
								<th>Point</th>
								<th>Customar</th>	
                                <th>Bank</th>
                                <th>Deposit Branch</th>
                                
								<th>Ref No</th>
                                <th>Method</th>
                                
								<th>Cheque Date</th>
                                <th>Deposit Amount</th>
                                <th>Actual Amount</th> 
                                <th>Bank Charge</th> 
								
								
								<th>Verify</th> 
                                <th>Remarks</th>
                                <th>Billing Remarks</th>
                                
								<th>Status</th>
								
								 
								
                               
								
                                
								<th>Upadated By</th>
							    
                            </tr>
							
                        </thead>
						
						<?php $tot_Depo_Amount = 0; ?> 	
                         @foreach($paymentList as $payment) 
						 
                        <tbody>
                            
                       <tr style="font-size: 11px; font-weight: normal;">
					   
						<th>{{date('d-m-Y',strtotime($payment->trans_date))}}</th>
						<th>{{$payment->sap_code}}</th>  
						<th>{{$payment->div_name}}</th>
						<th>{{$payment->point_name}}</th>  
                        <th>{{$payment->display_name}}</th>

                         <th>@if($payment->shortcode!=''){{$payment->shortcode . '::' . $payment->acctshortname . '::' . $payment->ssg_bank}}@else {{$payment->bank_name}} @endif</th> 
                       
					   <th>{{$payment->branch_name}}</th> 
                        <th>{{$payment->ref_no}}</th> 
                        <th>{{$payment->payment_type}}</th> 
                        <th>{{$payment->cheque_date}}</th>
                        <th>{{$payment->trans_amount}}</th>  
						
						<?php $tot_Depo_Amount += $payment->trans_amount;?>
						<th>{{$payment->net_amount}}</th>

						<th>{{$payment->bank_charge}}</th>
                        
						
                    	
							<th> 
								<div class="demo-radio-button">
										
										<input name="amtack[{{$payment->transaction_id}}]" type="checkbox" id="radio_yes[{{$dynLabel}}]" class="radio-col-red" value="CONFIRMED">
										<label for="radio_yes[{{$dynLabel}}]"> YES </label>
										
										<input type="hidden" name="reqid[]" value="{{$payment->transaction_id}}"  />
								</div>        
							   
							</th>
                        <th>{{$payment->payment_remarks}}</th>  
                        <th>{{$payment->ack_remarks}}</th>  
						<th>Acknowledge</th>  
						
						
						
							
							<th>
								Finance Dept
							</th>   
                        
						</tr>
						
						<input type="hidden" name="tran_id[{{$payment->transaction_id}}]" value="{{$payment->transaction_id}}">	
					 @php
                    $serial++;
                    $dynLabel++;
                    @endphp
                    @endforeach
					
					<tr>
                        <th colspan="10" style="text-align:center">Grand Total</th>
                        <th><?php echo '<b>' .  number_format($tot_Depo_Amount,0) . '</b>'; ?></th>
						<th colspan="6" style="text-align:center"></th>
			        </tr>
					
					
                @else
							
                            <tr>
                                <th colspan="13">No record found.</th>
                            </tr>
                         @endif   
						
								
                         
                          @if(sizeof($paymentList) > 0)   
                         
                        </tbody>
                        <tfoot>
                              <tr>

								<th>Date</th>
								<th>Sap Code</th>
								<th>Division</th>
								
								<th>Point</th>
								<th>Customar</th>	
                                <th>Bank</th>
                                <th>Deposit Branch</th>
                                
								<th>Ref No</th>
                                <th>Method</th>
                                
								<th>Cheque Date</th>
                                <th>Deposit Amount</th>
                                <th>Actual Amount</th> 
                                <th>Bank Charge</th> 
								
								
								<th>Verify</th> 
                                <th>Remarks</th>
                                <th>Billing Remarks</th>
                                
								<th>Status</th>
								
								 
								
                               
								
                                
								<th>Upadated By</th>
								
                                
                            </tr>
                            <tr>
                                   <th colspan="13" >
                                        <div class="col-sm-12" align="right">
                                            <input  type="submit" name="ORDER_VERIFY" value="VERIFY" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
                                        </div>  
                                    </th>

                                </tr>   
                                @endif
                                
                        </tfoot>
                    </table>    
				
				</div>	
					
				</form>	
              </div>

            </div>
        </div>
    </div>
</section>

@endsection 