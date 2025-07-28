<!-- Default Size -->

<form action="{{ URL('/territoryEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Territory Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                     <label for="division">Name of the Territory:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Territory" name="name" value="{{$territory_list->name}} " required="" />
                                        </div>
                                    </div>
                                     <label for="division">Division:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="division" required="">
                                        @foreach($division as $divisionSetup)
                                        <option value="{{$divisionSetup->div_id}}">{{$divisionSetup->div_name}}</option>
                                        @endforeach
                                        @foreach($divisionList as $divisionlists)
                                        <option value="{{$divisionlists->div_id}}">{{$divisionlists->div_name}}</option>
                                        @endforeach
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Company:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Company</option>
                                        @foreach($companyList as $rowCompany)
											@if($territory_list->global_company_id == $rowCompany->global_company_id)
												<option value="{{ $rowCompany->global_company_id }}" selected>{{ $rowCompany->global_company_name }}</option>
											@else
												<option value="{{ $rowCompany->global_company_id }}">{{ $rowCompany->global_company_name }}</option>
                                		   @endif;
										@endforeach
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Business Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="business_type_id" required="">
                                        <option value="">Select Business Type</option>
                                        @foreach($businessTypeList as $rowBusiness)
										  @if($territory_list->business_type_id == $rowBusiness->business_type_id)	
											<option value="{{ $rowBusiness->business_type_id }}">{{ $rowBusiness->business_type }}</option>
										  @else
											<option value="{{ $rowBusiness->business_type_id }}">{{ $rowBusiness->business_type }}</option>
										  @endif;
										@endforeach
                                        </select>

                                        </div>
                                    </div>
									
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{$territory_list->id}}">
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        
                    </form>
                    </div>
                </div>
            