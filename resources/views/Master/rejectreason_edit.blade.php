<!-- Default Size -->
<form action="{{ URL('/rejectreasonEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Reject Reason</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
								
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Reason" name="reason" value="{{ $id->reason }}" required="" />
                                        </div>
                                    </div>
                                      
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="reason_type" required="">
												<option value="">Select Reason Type</option>
											@if($id->reason_type == 1)   
												<option value="1" selected>VISIT</option>
											@else	
												<option value="1">VISIT</option>
											@endif
											
											@if($id->reason_type == 2)   
												<option value="2" selected>NON-VISIT</option>
											@else	
												<option value="2">NON-VISIT</option>
											@endif
											</select>
										</div>
                                    </div>
                                   
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="reason_status" required="">
												<option value="">Active/In-Active</option>
												
											@if($id->reason_status == 0)  	
												<option value="0" selected>ACTIVE</option>
											@else	
												<option value="0">ACTIVE</option>
											@endif
											
											@if($id->reason_status == 1)  
												<option value="1" selected>IN-ACTIVE</option>
											@else	
												<option value="1">IN-ACTIVE</option>
											@endif
											
											</select>
										</div>
                                    </div>
                        			
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                          <input type="hidden" class="form-control" name="user" value="{{ $id->user }}"/>
                          <input type="hidden" class="form-control" name="id" value="{{ $id->id }}"/>
                    </form>
                    </div>
                </div>
            