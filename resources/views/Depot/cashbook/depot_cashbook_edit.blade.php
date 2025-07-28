<!-- Default Size -->

<form action="{{ URL('/cashBookEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Expense Info</h4>
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
											<?php if($depotCashbook[0]->point_id == $rowDepot->point_id) { ?>
												<option value="{{ $rowDepot->point_id }}" selected>{{ $rowDepot->point_name }}</option>
											<?php } else { ?>
											    <option value="{{ $rowDepot->point_id }}">{{ $rowDepot->point_name }}</option>
											<?php }  ?>
											@endforeach
                                        </select>

                                        </div>
                                    </div>
                                	
									
									<label for="division">Expense Head:*</label>
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="perticular_head_id" required="">
											<option value="">Please Select Payment Type</option>
											
									<?php
										if(sizeof($ExPenseHead)>0)
										{										
											foreach($ExPenseHead as $rowExpenseHead)
											{  
											
											   if($rowExpenseHead->accounts_head_id == $depotCashbook[0]->perticular_head_id) { ?>
														
												<option value="<?php echo $rowExpenseHead->accounts_head_id ?>" selected><?php echo $rowExpenseHead->accounts_head_name ?></option>
												
											   <?php } else {?>
												<option value="<?php echo $rowExpenseHead->accounts_head_id ?>"><?php echo $rowExpenseHead->accounts_head_name ?></option>
												
									<?php		} // if closed
									
											} //loop closed 
									
										} // if closed
						
										?>										
											
											</select>

                                        </div>
                                    </div>
									
									
									<label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Amount" name="trans_amount"
											value ="{{$depotCashbook[0]->trans_amount}}"	required="" />
                                        </div>
                                    </div>
									
									<label for="division">Payment Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Date" id="fromdate" name="trans_date" 
											value="{{$depotCashbook[0]->trans_date}}" required="" />
                                        </div>
                                    </div>

									<label for="division">Note:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Note" value="{{$depotCashbook[0]->trans_description}}" name="trans_description" />
                                        </div>
                                    </div>									
									
									
                                  </div>
                                </div>
                            </div>
                        
						<input type="hidden" name="id" value="{{$depotCashbook[0]->cash_book_id}}">
                        
						<div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        </div>
                        
                    </form>
                    