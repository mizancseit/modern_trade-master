<!-- Default Size -->
<form action="{{ URL('/divisionEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Division</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Division Code" name="div_code" value="{{ $div_row->div_code }}" />
                                        </div>
                                    </div>
									
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Division Name" name="div_name" value="{{ $div_row->div_name }}" />
                                        </div>
                                    </div>
									
                                   
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="div_status" required="">
												<option value="">Active/In-Active</option>
												<option value="0">ACTIVE</option>
												<option value="1">IN-ACTIVE</option>
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
                          <input type="hidden" class="form-control" name="id" value="{{ $div_row->div_id }}"/>
                    </form>
                    </div>
                </div>
            