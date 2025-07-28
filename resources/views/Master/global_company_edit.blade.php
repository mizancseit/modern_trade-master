<!-- Default Size -->
<form action="{{ URL('/globalCompanyEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Global Company Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company" name="global_company_name" value="{{$globalCompanyList->global_company_name}}" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Owner" name="global_company_owner" value="{{$globalCompanyList->global_company_owner}}"  />
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Email" name="global_company_email" value="{{$globalCompanyList->global_company_email}}"  />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Phone" name="global_company_phone" value="{{$globalCompanyList->global_company_phone}}"  />
                                        </div>
                                    </div>
									
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Address" name="global_company_address" value="{{$globalCompanyList->global_company_address}}" />
                                        </div>
                                    </div>
									
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
						  <input type="hidden"  name="id" value="{{$globalCompanyList->global_company_id}}" />
						
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                    </div>
                </div>
            