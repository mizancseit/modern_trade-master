<!-- Default Size -->

<form action="{{ URL('/paymentEditProcess') }}" method="post" id="distriedit">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Payment Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <label for="division">Distributor:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="point_id" required="">
											<option value="">Please Select Distributor</option>
											@foreach($distriList as $rowDistri)
											<?php if($distriPayment[0]->point_id == $rowDistri->point_id) { ?>
												<option value="{{ $rowDistri->point_id }}" selected>{{ $rowDistri->point_name }}</option>
											<?php } else { ?>
											    <option value="{{ $rowDistri->point_id }}">{{ $rowDistri->point_name }}</option>
											<?php }  ?>
											@endforeach
                                        </select>

                                        </div>
                                    </div>
                                	
									
									<label for="division">Payment Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" id="payment_type_edit" name="payment_type" required="" onChange="distriBankDetailsEdit();">
                                        <option value="">Please Select Payment Type</option>
                                			<option value="CASH" <?= ($distriPayment[0]->payment_type=='CASH')?'SELECTED':''; ?>>CASH</option>
											<option value="CHEQUE" <?= ($distriPayment[0]->payment_type=='CHEQUE')?'SELECTED':''; ?>>CHEQUE</option>
											<option value="ON-LINE" <?= ($distriPayment[0]->payment_type=='ON-LINE')?'SELECTED':''; ?>>ON-LINE</option>
                                            <option value="PAY-ORDER" <?= ($distriPayment[0]->payment_type=='PAY-ORDER')?'SELECTED':''; ?>>PAY-ORDER</option>
                                            <option value="DD" <?= ($distriPayment[0]->payment_type=='DD')?'SELECTED':''; ?>>DD</option>
                                            <option value="TT" <?= ($distriPayment[0]->payment_type=='TT')?'SELECTED':''; ?>>TT</option>
								        </select>

                                        </div>
                                    </div>
									
									
									
							<div id="bank_div_edit" style="display:none;"> 	
								
									<label for="division">Select Bank:*</label>
									<div class="form-group">
                                        <div class="form-line">
											<select id="bank_name" name="bank_name" class="form-control show-tick">
													<option value="">Select Bank</option>
													<option value="AB Bank Limited" <?php if($distriPayment[0]->bank_name == 'AB Bank Limited') echo 'selected';?>>AB Bank Limited</option>
													<option value="Agrani Bank Limited" <?php if($distriPayment[0]->bank_name == 'Agrani Bank Limited') echo 'selected';?>>Agrani Bank Limited</option>
													<option value="Al-Arafah Islami Bank Limited" <?php if($distriPayment[0]->bank_name == 'Al-Arafah Islami Bank Limited') echo 'selected';?>>Al-Arafah Islami Bank Limited</option>
													<option value="Bangladesh Commerce Bank Limited" <?php if($distriPayment[0]->bank_name == 'Bangladesh Commerce Bank Limited') echo 'selected';?>>Bangladesh Commerce Bank Limited</option>
													<option value="Bangladesh Development Bank Limited" <?php if($distriPayment[0]->bank_name == 'Bangladesh Development Bank Limited') echo 'selected';?>>Bangladesh Development Bank Limited</option>
													<option value="Bangladesh Krishi Bank" <?php if($distriPayment[0]->bank_name == 'Bangladesh Krishi Bank') echo 'selected';?>>Bangladesh Krishi Bank</option>
													<option value="Bank Al-Falah Limited" <?php if($distriPayment[0]->bank_name == 'Bank Al-Falah Limited') echo 'selected';?>>Bank Al-Falah Limited</option>
													<option value="Bank Asia Limited" <?php if($distriPayment[0]->bank_name == 'Bank Asia Limited') echo 'selected';?>>Bank Asia Limited</option>
													<option value="BASIC Bank Limited" <?php if($distriPayment[0]->bank_name == 'BASIC Bank Limited') echo 'selected';?>>BASIC Bank Limited</option>
													<option value="BRAC Bank Limited" <?php if($distriPayment[0]->bank_name == 'BRAC Bank Limited') echo 'selected';?>>BRAC Bank Limited</option>
													<option value="Citibank N.A" <?php if($distriPayment[0]->bank_name == 'Citibank N.A') echo 'selected';?>>Citibank N.A</option>
													<option value="Commercial Bank of Ceylon Limited" <?php if($distriPayment[0]->bank_name == 'Commercial Bank of Ceylon Limited') echo 'selected';?>>Commercial Bank of Ceylon Limited</option>
													<option value="Dhaka Bank Limited" <?php if($distriPayment[0]->bank_name == 'Dhaka Bank Limited') echo 'selected';?>>Dhaka Bank Limited</option>
													<option value="Dutch-Bangla Bank Limited" <?php if($distriPayment[0]->bank_name == 'Dutch-Bangla Bank Limited') echo 'selected';?>>Dutch-Bangla Bank Limited</option>
													<option value="Eastern Bank Limited" <?php if($distriPayment[0]->bank_name == 'Eastern Bank Limited') echo 'selected';?>>Eastern Bank Limited</option>
													<option value="EXIM Bank Limited" <?php if($distriPayment[0]->bank_name == 'EXIM Bank Limited') echo 'selected';?>>EXIM Bank Limited</option>
													<option value="First Security Islami Bank Limited" <?php if($distriPayment[0]->bank_name == 'First Security Islami Bank Limited') echo 'selected';?>>First Security Islami Bank Limited</option>
													<option value="Habib Bank Ltd." <?php if($distriPayment[0]->bank_name == 'Habib Bank Ltd.') echo 'selected';?>>Habib Bank Ltd.</option>
													<option value="ICB Islamic Bank Ltd." <?php if($distriPayment[0]->bank_name == 'ICB Islamic Bank Ltd.') echo 'selected';?>>ICB Islamic Bank Ltd.</option>
													<option value="IFIC Bank Limited" <?php if($distriPayment[0]->bank_name == 'IFIC Bank Limited') echo 'selected';?>>IFIC Bank Limited</option>
													<option value="Islami Bank Bangladesh Ltd" <?php if($distriPayment[0]->bank_name == 'Islami Bank Bangladesh Ltd') echo 'selected';?>>Islami Bank Bangladesh Ltd</option>
													<option value="Jamuna Bank Ltd" <?php if($distriPayment[0]->bank_name == 'Jamuna Bank Ltd') echo 'selected';?>>Jamuna Bank Ltd</option>
													<option value="Janata Bank Limited" <?php if($distriPayment[0]->bank_name == 'Janata Bank Limited') echo 'selected';?>>Janata Bank Limited</option>
													<option value="Meghna Bank Limited" <?php if($distriPayment[0]->bank_name == 'Meghna Bank Limited') echo 'selected';?>>Meghna Bank Limited</option>
													<option value="Mercantile Bank Limited" <?php if($distriPayment[0]->bank_name == 'Mercantile Bank Limited') echo 'selected';?>>Mercantile Bank Limited</option>
													<option value="Midland Bank Limited" <?php if($distriPayment[0]->bank_name == 'Midland Bank Limited') echo 'selected';?>>Midland Bank Limited</option>
													<option value="Mutual Trust Bank Limited" <?php if($distriPayment[0]->bank_name == 'Mutual Trust Bank Limited') echo 'selected';?>>Mutual Trust Bank Limited</option>
													<option value="National Bank Limited" <?php if($distriPayment[0]->bank_name == 'National Bank Limited') echo 'selected';?>>National Bank Limited</option>
													<option value="National Bank of Pakistan" <?php if($distriPayment[0]->bank_name == 'National Bank of Pakistan') echo 'selected';?>>National Bank of Pakistan</option>
													<option value="National Credit & Commerce Bank Ltd" <?php if($distriPayment[0]->bank_name == 'National Credit & Commerce Bank Ltd') echo 'selected';?>>National Credit & Commerce Bank Ltd</option>
													<option value="NRB Commercial Bank Limited" <?php if($distriPayment[0]->bank_name == 'NRB Commercial Bank Limited') echo 'selected';?>>NRB Commercial Bank Limited</option>
													<option value="One Bank Limited" <?php if($distriPayment[0]->bank_name == 'One Bank Limited') echo 'selected';?>>One Bank Limited</option>
													<option value="Premier Bank Limited" <?php if($distriPayment[0]->bank_name == 'Premier Bank Limited') echo 'selected';?>>Premier Bank Limited</option>
													<option value="Prime Bank Ltd" <?php if($distriPayment[0]->bank_name == 'Prime Bank Ltd') echo 'selected';?>>Prime Bank Ltd</option>
													<option value="Pubali Bank Limited" <?php if($distriPayment[0]->bank_name == 'Pubali Bank Limited') echo 'selected';?>>Pubali Bank Limited</option>
													<option value="Rajshahi Krishi Unnayan Bank" <?php if($distriPayment[0]->bank_name == 'Rajshahi Krishi Unnayan Bank') echo 'selected';?>>Rajshahi Krishi Unnayan Bank</option>
													<option value="Rupali Bank Limited" <?php if($distriPayment[0]->bank_name == 'Rupali Bank Limited') echo 'selected';?>>Rupali Bank Limited</option>
													<option value="Shahjalal Bank Limited" <?php if($distriPayment[0]->bank_name == 'Shahjalal Bank Limited') echo 'selected';?>>Shahjalal Bank Limited</option>
													<option value="Shimanto Bank Limited" <?php if($distriPayment[0]->bank_name == 'Shimanto Bank Limited') echo 'selected';?>>Shimanto Bank Limited</option>
													<option value="Social Islami Bank Ltd." <?php if($distriPayment[0]->bank_name == 'Social Islami Bank Ltd.') echo 'selected';?>>Social Islami Bank Ltd.</option>
													<option value="Sonali Bank Limited" <?php if($distriPayment[0]->bank_name == 'Sonali Bank Limited') echo 'selected';?>>Sonali Bank Limited</option>
													<option value="South Bangla Agriculture & Commerce Bank Limited" <?php if($distriPayment[0]->bank_name == 'South Bangla Agriculture & Commerce Bank Limited') echo 'selected';?>>South Bangla Agriculture & Commerce Bank Limited</option>
													<option value="Southeast Bank Limited" <?php if($distriPayment[0]->bank_name == 'Southeast Bank Limited') echo 'selected';?>>Southeast Bank Limited</option>
													<option value="Standard Bank Limited" <?php if($distriPayment[0]->bank_name == 'Standard Bank Limited') echo 'selected';?>>Standard Bank Limited</option>
													<option value="Standard Chartered Bank" <?php if($distriPayment[0]->bank_name == 'Standard Chartered Bank') echo 'selected';?>>Standard Chartered Bank</option>
													<option value="State Bank of India" <?php if($distriPayment[0]->bank_name == 'State Bank of India') echo 'selected';?>>State Bank of India</option>
													<option value="The City Bank Ltd." <?php if($distriPayment[0]->bank_name == 'The City Bank Ltd.') echo 'selected';?>>The City Bank Ltd.</option>
													<option value="The Hong Kong and Shanghai Banking Corporation. Ltd." <?php if($distriPayment[0]->bank_name == 'The Hong Kong and Shanghai Banking Corporation. Ltd.') echo 'selected';?>>The Hong Kong and Shanghai Banking Corporation. Ltd.</option>
													<option value="Trust Bank Limited" <?php if($distriPayment[0]->bank_name == 'Trust Bank Limited') echo 'selected';?>>Trust Bank Limited</option>
													<option value="Union Bank Limited" <?php if($distriPayment[0]->bank_name == 'Union Bank Limited') echo 'selected';?>>Union Bank Limited</option>
													<option value="United Commercial Bank Limited" <?php if($distriPayment[0]->bank_name == 'United Commercial Bank Limited') echo 'selected';?>>United Commercial Bank Limited</option>
													<option value="Uttara Bank Limited" <?php if($distriPayment[0]->bank_name == 'Uttara Bank Limited') echo 'selected';?>>Uttara Bank Limited</option>
													<option value="Woori Bank" <?php if($distriPayment[0]->bank_name == 'Woori Bank') echo 'selected';?>>Woori Bank</option>
												</select>
											</div>
										</div>
									</div>
									 <div id="online_divs" style="display:none">    
                                
                                    <label for="division">Select SSG Bank:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <?php
                                                //echo $user_business_type;
                                                        
                                            $ssg_bank=DB::select("select * from tbl_master_bank where business_type=$user_business_type and status=0");

                                          
                                             ?>
                                            <select id="bank_name" name="ssgbank_name" class="form-control show-tick">          <?php foreach( $ssg_bank as $bank){?>
                                                    <option value="<?php echo $bank->bank_name ?>"<?php if($distriPayment[0]->bank_name == $bank->bank_name) echo 'selected';?>><?php  echo $bank->acctshortname."::".$bank->shortcode."::".$bank->bank_name;}?></option>
                                                   
                                    </select>
                                   </div>
                                 </div>
                               </div>
									<div id="bank_childs" style="display: none;">
									<label for="division">Branch:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Branch Name" name="branch_name" value="{{$distriPayment[0]->branch_name}}" />
                                        </div>
                                    </div>
									
									<label for="division">Account No:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Accounts No" name="acc_no" value="{{$distriPayment[0]->acc_no}}" />
                                        </div>
                                    </div>
									
									
									<label for="division">Cheque No:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Cheque No" name="cheque_no" value="{{$distriPayment[0]->cheque_no}}" />
                                        </div>
                                    </div>
									
									<label for="division">Cheque Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Cheque Date" id="fromdate" name="cheque_date" value="{{$distriPayment[0]->cheque_date}}" />
                                        </div>
                                    </div>
							</div>	
									<!-- Maung Added Field-->
                                   <div id="ref_no" style="display: none;">
                                    <label for="division">REF.NO</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="ref_no"  value="{{$distriPayment[0]->ref_no}}"/>
                                        </div>
                                    </div>
                                </div>
                                    
                                    <!-- Maung Added Field-->			

									
									<label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Amount" name="trans_amount"
											value ="{{$distriPayment[0]->trans_amount}}"	required="" />
                                        </div>
                                    </div>
									
									<label for="division">Payment Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Date" name="trans_date" 
											value="{{$distriPayment[0]->trans_date}}" required="" />
                                        </div>
                                    </div>

									<label for="division">Remarks/Note:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Payment Remarks" id="todate" 
											name="payment_remarks" value="{{$distriPayment[0]->payment_remarks}}"/>
                                        </div>
                                    </div>	
									
									
                                  </div>
                                </div>
                            </div>
                        
						<input type="hidden" name="id" value="{{$distriPayment[0]->transaction_id}}">
                        
						<div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="distriCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        </div>
                        
                    </form>
                    