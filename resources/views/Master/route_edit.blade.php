<!-- Default Size -->

<form action="{{ URL('/route_process') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
               <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalNew">Edit Route </h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                
								<div class="col-sm-12">
                                   
                                        
									<label for="division">Select Point:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="point_id" required="">
                                        <option value="">Please Select Point</option>
                                        @foreach($pointList as $rowPoint)
                                          @foreach($routeList as $routeListNew)
                                        @if($routeListNew->point_id == $rowPoint->point_id)
                                                <option value="{{ $rowPoint->point_id }}" selected>{{ $rowPoint->point_name }}</option>
                                            @else
                                                <option value="{{ $rowPoint->point_id }}">{{ $rowPoint->point_name }}</option>
                                            @endif
                                        
                                         @endforeach
                                          @endforeach
                                        </select>

                                        </div>
                                    </div>											
        
                                    <label for="division">Name Of the Route:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                             @foreach($routeList as $routeListNew)
                                            <input type="text" class="form-control" placeholder="Route Name" name="rname" value="{{$routeListNew->rname}}" id="rname" required="" />
                                             @endforeach
                                        </div>
                                    </div>
                                    
									<label for="division">Route Details:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Route Details" name="details" value="{{$routeListNew->details}}" id="details" />
                                        </div>
                                    </div> 

									<label for="division">Company:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Company</option>
                                          @foreach($companyList as $rowCompany)
                                         @foreach($routeList as $routeListNew)
                                        
                                          
                                            @if($routeListNew->global_company_id == $rowCompany->global_company_id)
                                                <option value="{{ $routeListNew->global_company_id }}" selected>{{ $routeListNew->company_name }}</option>
                                            @else
                                                <option value="{{ $rowCompany->global_company_id }}">{{ $rowCompany->global_company_name}}</option>
                                            @endif
                                         @endforeach
                                           @endforeach
                                        </select>
                                        </div>
                                    </div>									
 
                                </div>
								
                            </div>
                        </div>
						<input type="hidden" name="route_id" value="{{$routeListNew->route_id}}">
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
					  </form>
                    </div>
                </div>
				
				
            