<!-- Default Size -->
<form action="{{ URL('/companyEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Company Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="SAP Code" name="sap_code" value="{{ $id->sap_code }}" required="" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Company" name="name" value="{{ $id->name }}" required="" />
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Address" name="address" value="{{ $id->address }}" required="" />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Mobile" name="mobile" value="{{ $id->mobile }}" required="" />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="T&T" name="tnt" value="{{ $id->tnt }}" required="" />
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
            