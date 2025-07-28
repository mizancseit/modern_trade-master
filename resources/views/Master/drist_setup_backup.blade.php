@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DISTRIBUTOR INFORMATION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Distributor
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
            Distributor Set Up
        </h2>
    </div>

    
         
         
       
            <br>
            <div class="body">
<div id="distri">
            
             <form action="{{ URL('/distributor_process') }}" method="post" >
                {{ csrf_field() }}    <!-- token --> 
                 <div class="row clearfix">
                   <div class="col-md-4">
                                    <p>
                                        <b>Name of the Distributor:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" style="text-transform: capitalize;" class="form-control" placeholder="Owners Name" name="dname" id="dname" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>SAP Code:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="SAP Code" name="sapcode" id="sapcode" required="" onkeyup="sync()" />
                                                 
                                        </div>
                                        
                                    </div>
                                </div>
								
								<div class="col-md-4">
                               
                                    <p>
                                        <b>Business Type:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                        	<div id="btype">

                                       </div>
                                       <select class="show-tick" data-live-search="true" name="business_type_id" required="" ">
                                        <option value="" id="btype">Please Select Type</option>
                                        @foreach($business_type as $type_setup)
                                        <option value="{{$type_setup->business_type_id}}">{{$type_setup->business_type}}</option>
                                        @endforeach
                                        </select>
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
                                        	 <div id="divName">

                                       </div>
                                       
                                       <select class="show-tick" data-live-search="true" name="div_id" required="" ">
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
                                        <b>Point:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                            
                                         
                                          <!--<selcet id="pointId"  class="show-tick" data-live-search="true"></selcet>-->
                                           <div id="po">

                                       </div>
                                       
                                                   
                                 <select class="show-tick" data-live-search="true" name="point_id" required="" id="">

                                      
                                      <option value="" id=""> Please Select Point</option>

                                        @foreach($point as $point_setup)
                                        <option value="{{$point_setup->point_id}}">{{$point_setup->point_name}}</option>
                                        @endforeach
                                        
                                        </select>
                                        </div>
                                </div>
                            </div>
                             
                            <div class="col-md-4">
                                    <p>
                                        <b>Mobile No:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">(+880)
                                            <input type="text" maxlength="10" class="form-control" placeholder="Mobile Number" name="mobile_no" id="mobile_no" required="" />
                                     </div>
                                        
                                    </div>
                                </div>
                            </div>
                             <div class="row clearfix">
                                 <div class="col-md-4">
                                    <p>
                                        <b>T&T:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" maxlength="7" class="form-control" placeholder="T&T Number" name="tnt" id="tnt" required="" />
                                     </div>
                                        
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <p>
                                        <b>Credit Limit:</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Credit Limit" name="credit_limit" id="creditlimit"/>
                                     </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Email:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="email" class="form-control" placeholder="Email" name="email" id="email" required="" />
                                     </div>
                                        
                                    </div>
                                </div>
                                
                            </div>
                             <div class="row clearfix">
                                  <div class="col-md-4">
                                   
                                    <p>
                                        <b>Pricing:</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                        	 <div id="ptype">

                                       </div>
                                       
                                       <select class="show-tick" data-live-search="true" name="price_type">
                                        <option value="" id="price_type ">Please Select Pricing</option>
                                        <option value="distributor">Distributor</option>
                                        <option value="depot">Depot</option>
                                         <option value="MRP">MRP</option>
                                        </select>
                                        </div>
                                </div>
                            </div>
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
                                        <b>User Name:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="User Name" name="username" id="username" required="" disabled="" />
                                     </div>
                                        
                                    </div>
                                </div>
             
                               <div class="col-md-4">
                                    <p>
                                        <b>Password:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line">
                                            <input type="password" class="form-control" placeholder="Password" name="password" id="password" required="" />
                                     </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Address:*</b>
                                    </p>
                                   <div class="input-group">
                                        <div class="form-line">
                                            <textarea class="form-control" name="address" id="address"></textarea>
                                                 
                                        </div>
                                    </div>
                                </div>
								
								 <div class="col-md-4">
                                    <p>
                                        <b>Company:*</b>
                                    </p>
                                   <div class="input-group">
                                       <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        
                                        @foreach($companyList as $rowCompany)
                                        <option value="{{ $rowCompany->global_company_id }}">{{ $rowCompany->global_company_name }}</option>
                                         @endforeach
                                        </select>

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
                        <th>Business Type</th>
                        <th>Name</th>
                        <th>Point</th>
                        <th>Division</th>
                        <th>Mobile</th>
                        <th>Type</th>
                        <th>CLimit</th>
                        <th>Pricing</th>
                        
                     
                        
                        <th class="">Action</th>
                    </tr>
                </thead>
                 <tbody>
                    @if(sizeof($distri_details) > 0)   
                    @php
                    $serial =1;
                    @endphp


                    @foreach($distri_details as $distri_details_view) 

                    
                    <tr>
					    <th>{{$serial}}</th>
                        <th>{{$distri_details_view->company_name }}</th>
                        
						<th>{{$distri_details_view->business_type }}</th>
                        
						<th>{{$distri_details_view->display_name }}</th>
                          <?php 
                      $point_name=DB::table('tbl_point')->where('point_id',$distri_details_view->point_id)->get();
                      
                      //dd($div_name);
                      ?>
                     
                       <th><?php 
                       foreach($point_name as $name){
                       echo $name->point_name;
                   }?>
                   </th>
                         <th><?php 
                       foreach($point_name as $name){
                        $div_name=DB::table('tbl_division')->where('div_id',$name->point_division)->first();
                       echo $div_name->div_name;
                   }?>
                   </th>
                   </th> <th>{{$distri_details_view->cell_phone }}</th>
                        <th>{{$distri_details_view->business_type_id }}</th>
                        <th>{{$distri_details_view->credit_limit }}</th>
                        <th>{{$distri_details_view->price_type }}</th>
                        
                    

                      
                       </th>
                       <th><input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDistri('{{ $distri_details_view->id }}')" style="width: 70px;""><br>
                        <input type="button" name="route_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDistri('{{ $distri_details_view->id }}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>
                   
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="9">No record found.</th>
                    </tr>
                @endif     
                  
               
                </tbody>
                <tfoot>
                    <tr>
                        
                       <th>SL</th>
                        <th>Company Name</th>
                        <th>Business Type</th>
                        <th>Name</th>
                        <th>Point</th>
                        <th>Division</th>
                        <th>Mobile</th>
                        <th>Type</th>
                        <th>CLimit</th>
                        <th>Pricing</th>
                        
                     
                        
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
    <script>
    function sync()
{
  var n1 = document.getElementById('sapcode');
  var n2 = document.getElementById('username');
  n2.value = n1.value;
}
</script>
@endsection

