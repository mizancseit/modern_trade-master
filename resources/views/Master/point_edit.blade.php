<!-- Default Size -->

<form action="{{ URL('/pointEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Point Info</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                      <label for="division">Division:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="point_division" required="" onchange="getTerritory(this.value)">
                                       
                                        @foreach($division as $divisionName)
                                        <option value="{{ $divisionName->div_id }}">{{ $divisionName->div_name }}</option>
                                         @endforeach
                                           @foreach($divisionList as $divisionlists)
                                        <option value="{{$divisionlists->div_id}}">{{$divisionlists->div_name}}</option>
                                        @endforeach
                                      
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Territory:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="territory_id" required="">
                                        <option value="">Please Select Territory</option>
                                        @foreach($territoryList as $rowTerritory)
											@if($pointList->territory_id == $rowTerritory->id)
												<option value="{{ $rowTerritory->id }}" selected>{{ $rowTerritory->name }}</option>
											@else
												<option value="{{ $rowTerritory->id }}">{{ $rowTerritory->name }}</option>
											@endif;
                                        @endforeach
                                        </select>

                                        </div>
                                    </div>		
                                    
									
									<label for="division">Name Of the Point:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Point Name" name="point_name" value="{{$pointList->point_name}}" required="" />
                                                 
                                         
                                        </div>
                                    </div>
									
									<label for="division">Company:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Company</option>
                                        @foreach($companyList as $rowCompany)
											@if($pointList->global_company_id == $rowCompany->global_company_id)
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
										@if($pointList->business_type_id == $rowCompany->global_company_id)
											<option value="{{ $rowBusiness->business_type_id }}" selected>{{ $rowBusiness->business_type }}</option>
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
                         <input type="hidden" name="id" value="{{$pointList->point_id}}">
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        </div>
                        
                    </form>
                    