@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            RETAILER INFORMATION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Retailer
                            </small>
                        </h2>
                    </div>
                </div>
            </div>

             @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
           
        
            <div class="row clearfix">
               
            
            <!-- #END# Exportable Table -->
      
           <div class="card">
    <div class="header">
        <h2>
            RETAILER Setup
        </h2>
    </div>

    
         
         
       
            <br>
            <div class="body">
<div id="distri">
            
             <form action="{{ URL('/retailer_process') }}" method="post" >
                {{ csrf_field() }}    <!-- token --> 
                 <div class="row clearfix">
							
							<div class="col-md-4">
                                    <p>
                                        <b>Name of the Retailer:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
										<div class="form-line">
                                            <input type="text" class="form-control" placeholder="Retailer Name" name="name" id="retailer_name" required="" />
                                        </div>
									</div>
                            </div>
                        	
							
							<div class="col-md-4">
                                    <p>
                                        <b>OWNER Name:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="OWNER Name" name="owner" id="owner_name" required="" />
                                        </div>
                                        
                                    </div>
                            </div>
							
							<div class="col-md-4">
                                    <p> 
										<b>Address:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Address" name="vAddress" id="address" />
                                        </div>
                                    </div>
                            </div>
							
				</div>
               
                
				<div class="row clearfix">
                    <div class="col-md-4">
                         
                                    <p>
                                        <b>Division:*</b>
                                    </p>
									
                                    <div class="input-group">
                                        <div class="form-line"> 
                                        	 <div id="divName"></div>
                                       
                                       <select class="show-tick" data-live-search="true" name="division" required="" ">
                                        <option value="" id="div_id">Please Select Division</option>
                                        @foreach($division as $division_setup)
                                        <option value="{{$division_setup->div_id}}">{{$division_setup->div_name}}</option>
                                        @endforeach
                                        </select>
                                        </div>
									</div>
                    </div>
					
					<div class="col-md-4">
				   
						<p>
							<b>Point Name:*</b>
						</p>
						<div class="input-group">
							<div class="form-line"> 
							
								<div id="pointName"></div>
								
						   <select class="show-tick" data-live-search="true" name="point_id" required="">
							<option value="" id="btype">Please Select Type</option>
							@foreach($point_list as $point_row)
							<option value="{{$point_row->point_id}}">{{$point_row->point_name}}</option>
							@endforeach
							</select>
							</div>
						</div>
					</div>
					
					
					<div class="col-md-4">
				   
						<p>
							<b>Route:*</b>
						</p>
						<div class="input-group">
							<div class="form-line"> 
								
								<div id="routeName"></div>
								
						   <select class="show-tick" data-live-search="true" name="rid" required="" ">
							<option value="" id="btype">Please Select Type</option>
							@foreach($route_list as $route_list_row)
							<option value="{{$route_list_row->route_id}}">{{$route_list_row->rname}}</option>
							@endforeach
							</select>
							</div>
						</div>
					</div>
					
	                        
							
                </div>
                             
							 
							 <div class="row clearfix">
							 
								<div class="col-md-4">
                                    <p>
                                        <b>Mobile No:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" id="mobile" required="" />
                                     </div>
                                        
                                    </div>
                                </div>
							 
							 
                                 <div class="col-md-4">
                                    <p>
                                        <b>T&T:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="T&T Number" name="tnt" id="tnt" />
                                     </div>
                                        
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <p>
                                        <b>Email:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="email" class="form-control" placeholder="Email" name="email" id="email" />
                                     </div>
                                        
                                    </div>
                                </div>
								
								
                                
                            </div>
							
                             <div class="row clearfix">
                                  
                                 <div class="col-md-4">
                                    <p>
                                        <b>Date of birth:</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="date" class="form-control" placeholder="Please choose a date..." name="dob" id='datetimepicker1'/>
                                     </div>
                                        
                                    </div>
                                </div>
								
								<div class="col-md-4">
				   
									<p>
										<b>Retailer Type:*</b>
									</p>
									
									<div class="input-group">
										<div class="form-line"> 
											
											<div id="retailerType"></div>
											
											<select class="show-tick" data-live-search="true" name="shop_type" required="">
										
												<option value="" id="btype">Please Select Type</option>
												<option value="0">END-SHOP</option>
												<option value="1">Dealer</option>
										
											</select>
											
										</div>
									</div>
								</div>	
								
								<div class="col-md-4">
									<p>
										<b>Retailer Status:*</b>
									</p>
									
									<div class="input-group">
										<div class="form-line"> 
											
											<div id="retailerStatus"></div>
									   
											<select class="show-tick" data-live-search="true" name="status" required="">
												<option value="" id="btype">Active-Inactive</option>
												<option value="0">Active</option>
												<option value="1">In-Active</option>
											
											</select>
										</div>
									</div>
									
								</div>
                                
                               

                            </div>
							
							 
					<div class="row clearfix">		
							
							
							
							<div class="col-md-4">
                         
                                    <p>
                                        <b>Company:*</b>
                                    </p>
									
                                    <div class="input-group">
                                        <div class="form-line"> 
                                        	 <div id="divName"></div>
                                       
                                       <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Company</option>
                                        @foreach($companyList as $rowCompany)
                                        <option value="{{ $rowCompany->global_company_id }}">{{ $rowCompany->global_company_name }}</option>
                                         @endforeach
                                        </select>
                                        </div>
									</div>
							</div>

                            <div class="col-md-4">
                         
                                    <p>
                                        <b>Serial:*</b>
                                    </p>
                                    
                                    <div class="input-group">
                                        <div class="form-line"> 
                                             <div id="divName"></div>
                                                <input type="number" name="serial" id="serial" class="form-control show-tick" required="" minlength="1">
                                       
                                        </div>
                                    </div>
                            </div>
							
                            
                              
					</div>
                           
						   <input type="hidden" class="form-control" placeholder="" name="id" id="id"  />
                         
                            <div class="text-center">
                            
								<button type="submit"  id="" class="btn btn-default btn-lg waves-effect m-r-20 center-block"><b>Save</b></button>
                          
							</div>
                        
  
            </form>
</div>
        </div>

            <br>
            <div class="body">

    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                      
                        <th>SL</th>
						<th>Company Name</th>
                        <th>Retailer Name</th>
                        <th>Mobile</th>
                        <th>Division</th>
                        <th>Point</th>
						
                        <th>Route</th>
                        <th>Status</th>
                        
                        <th class="">Action</th>
                    </tr>
                </thead>
				
                 <tbody>
                    @if(sizeof($retailer_details) > 0)   
                    @php
                    $serial =1;
                    @endphp


                    @foreach($retailer_details as $retailer_details_view) 

                    
                    <tr>
					    <th>{{$serial}}</th>
                        
						<th>{{$retailer_details_view->company_name }}</th>
                        
						<th>{{$retailer_details_view->name }}</th>
						
						<th>{{$retailer_details_view->mobile }}</th>
						
						<th>{{$retailer_details_view->div_name }}</th>
						
						<th>{{$retailer_details_view->point_name }}</th>
				   
						<th>{{$retailer_details_view->rname }}</th>
						
						@if($retailer_details_view->status == 0)   
							 <th>Active</th>
						@else	
							 <th>In-Active</th>
						@endif	
						
						
                      
                       <th><input type="button" name="retailer_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editRETAILER('{{ $retailer_details_view->retailer_id }}')" style="width: 70px;""><br/>
                       <input type="button" name="retailer_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteRETAILER('{{ $retailer_details_view->retailer_id }}')" style="width: 70px; margin-top: 0px;"></th>
                    
					</tr>
                   
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="6">No record found.</th>
                    </tr>
                @endif     
                  
               
                </tbody>
				
                <tfoot>
                    <tr>
                         <th>SL</th>   
                        <th>Company Name</th>
                        <th>Retailer Name</th>
                        <th>Mobile</th>
                        <th>Division</th>
                        <th>Point</th>
						
                        <th>Route</th>
                        <th>Status</th>
                        
                      
                        <th class="">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>

</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
