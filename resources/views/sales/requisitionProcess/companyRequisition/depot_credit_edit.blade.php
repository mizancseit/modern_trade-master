<!-- Default Size -->

<form action="{{ URL('/paymentEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Payment Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <label for="division">DEPOT:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="point_id" required="">
											<option value="">Please Select Depot</option>
											@foreach($depotList as $rowDepot)
											<?php if($depotPayment[0]->point_id == $rowDepot->point_id) { ?>
												<option value="{{ $rowDepot->point_id }}" selected>{{ $rowDepot->point_name }}</option>
											<?php } else { ?>
											    <option value="{{ $rowDepot->point_id }}">{{ $rowDepot->point_name }}</option>
											<?php }  ?>
											@endforeach
                                        </select>

                                        </div>
                                    </div>
                                	
									
									<label for="division">Payment Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="payment_type" required="">
                                        <option value="">Please Select Payment Type</option>
                                        
											<option value="ON-LINE" <?= ($depotPayment[0]->payment_type=='ON-LINE')?'SELECTED':''; ?>>ON-LINE</option>
											<option value="CASH" <?= ($depotPayment[0]->payment_type=='CASH')?'SELECTED':''; ?>>CASH</option>
											<option value="CHEQUE" <?= ($depotPayment[0]->payment_type=='CHEQUE')?'SELECTED':''; ?>>CHEQUE</option>
											<option value="PAY-ORDER" <?= ($depotPayment[0]->payment_type=='PAY-ORDER')?'SELECTED':''; ?>>PAY-ORDER</option>
											<option value="DD" <?= ($depotPayment[0]->payment_type=='DD')?'SELECTED':''; ?>>DD</option>
											<option value="TT" <?= ($depotPayment[0]->payment_type=='TT')?'SELECTED':''; ?>>TT</option>
                                        
                                        </select>

                                        </div>
                                    </div>
									
									
									<label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Amount" name="trans_amount"
											value ="{{$depotPayment[0]->trans_amount}}"	required="" />
                                        </div>
                                    </div>
									
									<label for="division">Payment Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Date" name="trans_date" 
											value="{{$depotPayment[0]->trans_date}}" required="" />
                                        </div>
                                    </div>								
									
									
                                  </div>
                                </div>
                            </div>
                        
						<input type="hidden" name="id" value="{{$depotPayment[0]->transaction_id}}">
                        
						<div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        </div>
                        
                    </form>
                    