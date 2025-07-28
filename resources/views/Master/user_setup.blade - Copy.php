@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            User Mangement
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / User Management
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
            User Management Set Up
        </h2>
    </div>

    <div class="body">
          <!-- Tabs With Icon Title -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                User Management Panel
                            </h2>
                           
                        </div>
                        <div class="body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home_with_icon_title" data-toggle="tab">
                                        <i class="material-icons">home</i> User Basic Info
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#profile_with_icon_title" data-toggle="tab">
                                        <i class="material-icons">face</i> User Details
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#messages_with_icon_title" data-toggle="tab">
                                        <i class="material-icons">email</i> User Business scope
                                    </a>
                                </li>
                                
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="home_with_icon_title">
                                    <b>User Creation</b>
                                    <p>
                                      <form action="{{ URL('/userSetup') }}" method="post" >
                {{ csrf_field() }}    <!-- token --> 
                 <div class="row clearfix">
                   <div class="col-md-4">
                                    <p>
                                        <b>username:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Username" name="email" id="email" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p>
                                        <b>Password:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="password" class="form-control" placeholder="Enter Password" name="password" id="password" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Display Name:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter display name" name="display_name" id="display_name" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                   <div class="col-md-4">
                                    <p>
                                        <b>Employee Id:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="number" class="form-control" placeholder="Enter Employee Id" name="employee_id" id="employee_id" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <p>
                                        <b>Designation:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Designation" name="designation" id="designation" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="test">
                               
                                    <p>
                                        <b>User Type:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                            <div id="userType">

                                       </div>
                                       <select class="show-tick" data-live-search="true" name="user_type_id" required="" ">
                                        <option value="" id="btype">Please Select User Type</option>
                                        @foreach($userType as $user_type)
                                        <option value="{{$user_type->user_type_id}}">{{$user_type->user_type}}</option>
                                        @endforeach
                                        </select>
                                        </div>
                                </div>
                            </div>
                            </div>
                             <div class="row clearfix">
                               <div class="col-md-4" id="business_type">
                               
                                    <p>
                                        <b>Business Type:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                            <div id="busiType">

                                       </div>
                                       <select class="show-tick" data-live-search="true" name="business_type_id" required="" ">
                                        <option value="" id="btype">Please Select Business Type</option>
                                        @foreach($businessType as $business_type)
                                        <option value="{{$business_type->business_type_id}}">{{$business_type->business_type}}</option>
                                        @endforeach
                                        </select>
                                        </div>
                                </div>
                            </div> 
                            <div class="col-md-4" id="company">
                               
                                    <p>
                                        <b>Company Name:*</b>
                                    </p>
                                    <div class="input-group">
                                        <div class="form-line"> 
                                            <div id="company">

                                       </div>
                                       <select class="show-tick" data-live-search="true" name="global_company_id"  ">
                                        <option value="" id="btype">Please Select Company</option>
                                       @foreach($globalCompany as $company)
                                        <option value="{{$company->global_company_id}}">{{$company->global_company_name}}</option>
                                        @endforeach
                                        </select>
                                        </div>
                                </div>
                            </div> 
                               <div class="col-md-4">
                                    <p>
                                        <b>SAP Code:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter SAP code" name="sap_code" id="sap_code" />
                                                 
                                        </div>
                                    </div>
                                </div>

                                 <div class="col-md-4">
                                    <p>
                                        <b>Date of Joining:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="date" class="form-control" placeholder="Enter date of joining" name="doj" id="doj" />
                                                 
                                        </div>
                                    </div>
                                </div>
                             </div>
                               <input type="hidden" class="form-control" placeholder="" name="id" id="id1"  />
                                <div class="text-center">
                            <button type="submit"  id="" class="btn btn-default btn-lg waves-effect m-r-20 center-block"><b>Save</b></button>
                          </div>
                        <!-- User Basic Details Starts -->
                          <?php 
                           //New add 250218 
                            $user_id=Auth::user()->id;
                            $user=DB::table('users')
                                                ->where('id', $user_id)
                                                ->first();

                            $user_type=Auth::user()->user_type_id;  //$user->user_type_id;

                            if(Auth::user()->user_type_id==4)
                            {
                                //echo '4';
                              $users=DB::select("SELECT * FROM users where user_type_id not in(1,2,3)");
                            }
                            elseif(Auth::user()->user_type_id==2)
                            {
                                //echo '2';
                              $users=DB::select("SELECT * FROM users WHERE user_type_id NOT IN('1,2,3')");
                            }                          
                            else 
                            {
                                //echo 'Yes';
                              $users=DB::select("SELECT * FROM users");
                            }
                            ?>

                            <br>
                         <div class="table-responsive">
                          
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Display Name</th>
                        <th>Employee Id</th>
                        <th>Designation</th>
                        <th>User Type</th>
                        <th>Business Type</th>
                        <th>Joining Date</th>
                        
                        <th class="">Action</th>
                    </tr>
                </thead>
                 <tbody>
              @foreach($users as $usersInfo) 
                    <tr>
                        <th>{{$usersInfo->email}}</th>
                        <th>{{$usersInfo->display_name}}</th>
                        <th>{{$usersInfo->employee_id}}</th>
                        <th>{{$usersInfo->designation}}</th>
                          <?php $user_type_no=DB::select("SELECT * FROM tbl_user_type where user_type_id='$usersInfo->user_type_id' ");
                         //dd($user_type_no);
                           foreach ($user_type_no as $id) {
                               # code...
                           
                               $user_type_id=$id->user_type;
                                
                            }
                            $business_type_no=DB::select("SELECT * FROM tbl_business_type where business_type_id='$usersInfo->business_type_id' ");
                            foreach ($business_type_no as $type) {
                               # code...
                           
                               $business_type_id=$type->business_type;
                                
                            }
                           ?>
                           
                        <th>{{ $user_type_id}}</th>
                        <th>{{ $business_type_id}}</th>
                        <th>{{$usersInfo->doj}}</th>
                        
                        <th><input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editUserbasic('{{$usersInfo->id}}')" style="width: 70px;"">
                        <input type="button" name="route_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteUserbasic('{{$usersInfo->id}}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>
                   
                
                 
             @endforeach
               
                   
                
               
                </tbody>
                <tfoot>
                    <tr>
                       <th>User Name</th>
                        <th>Display Name</th>
                        <th>Employee Id</th>
                        <th>Designation</th>
                        <th>User Type</th>
                        <th>Business Type</th>
                      
                        <th>SAP Code</th>
                        <th class="">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>






                        <!-- User Basic Details Ends-->
                           

                                    </p>
                                </div>
                           

                            </form>
                                <div role="tabpanel" class="tab-pane fade" id="profile_with_icon_title">
                                    <b>User Details</b>
                                    <p>
                                         <form action="{{ URL('/userDetails') }}" method="post" >
                {{ csrf_field() }}    <!-- token --> 
                                          <div class="row clearfix">
                   <div class="col-md-4">
                    @if(Session::has('username'))
                    <?php 
                    $user_email= Session::get('username');
                    //echo $user_email;
                    //echo"hello";
                    
                   
                      $user=DB::table('users')
                    ->where('email',$user_email)
                    ->first();
    // dd($user); 
                    if(isset($user)){
                    $user_id=$user->id;
                     }
                     else{
                       $user_id=1; 
                     }
                    
                     ?>
                      @endif
                     
                                    <p>
                                        <b>First Name:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter  First Name" name="first_name" id="first_name"  />
                                                 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p>
                                        <b>Middle Name:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Middle Name" name="middle_name" id="middle_name" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Last Name:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Last name" name="last_name" id="last_name" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                            </div>
                              <div class="row clearfix">
                   <div class="col-md-4">
                                    <p>
                                        <b>Owner Name:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Owner Name" name="owner_name" id="owner_name"  />
                                                 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p>
                                        <b>Land Phone:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Land Phone" name="land_phone" id="land_phone" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Cell Phone:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter  Cell Phone" name="cell_phone" id="cell_phone" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                   <div class="col-md-4">
                                    <p>
                                        <b>Current Address</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <textarea  class="form-control" name="current_address" id="current_address" ></textarea>
                                                 
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <p>
                                        <b>Permanent Address</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <textarea  class="form-control" name="permanent_address" id="permanent_address" ></textarea>
                                                 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p>
                                        <b>Email:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter Email Address" name="email" id="email" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row clearfix">
                             <div class="col-md-4">
                                    <p>
                                        <b>DOB:*</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="date" class="form-control" placeholder="Enter DOB" name="dob" id="dob" required="" />
                                                 
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <p>
                                        <b>SAP CODE:</b>
                                    </p>
                                    <div class="input-group input-group-lg">
                                       
                                      <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Enter SAP Code" name="sap_code" id="sap_code"  />
                                                 
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="text-center">
                                 <input type="hidden" name="user_id" value="<?php if(isset($user_id)) {echo $user_id;} ?>">
                                 
                            <button type="submit"  id="" class="btn btn-default btn-lg waves-effect m-r-20 center-block"><b>Save</b></button>
                          </div><!-- User  Details Starts -->
                          <?php 
                           $user_details=DB::select("SELECT * FROM tbl_user_details");
                         

                            ?>
                            <br>
                         <div class="table-responsive">
                          
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Owner Name</th>
                        <th>Land Phone</th>
                        <th>Cell Phone</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>SAP Code</th>
                        
                        <th class="">Action</th>
                    </tr>
                </thead>
                 <tbody>
              @foreach($user_details as $userDetails) 
                    <tr>
                        <?php 
                        $user_name=DB::select(" select * from users where id='$userDetails->user_id'");
                        foreach ( $user_name as $name) {
                               # code...
                           
                                $name=$name->email;
                                
                            }?>
                         <th>{{$name}}</th>
                        <th>{{$userDetails->first_name}}</th>
                        <th>{{$userDetails->middle_name}}</th>
                        <th>{{$userDetails->last_name}}</th>
                        <th>{{$userDetails->owner_name}}</th>
                        <th>{{$userDetails->land_phone}}</th>
                        <th>{{$userDetails->cell_phone}}</th>
                        <th>{{$userDetails->email}}</th>
                        <th>{{$userDetails->dob}}</th>
                        <th>{{$userDetails->sap_code}}</th>
                        <th><!--<input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editUserDetails()" style="width: 70px;"">-->
                        </th>
                    </tr>

                    <!-- User  Details Ends -->
                   
                
                 
             @endforeach
               
             </tbody>
                
                <tfoot>
                    <tr>
                        <th>User Name</th>
                       <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Owner Name</th>
                        <th>Land Phone</th>
                        <th>Cell Phone</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>SAP Code</th>
                        <th class="">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
                                    </p>
                                </form>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="messages_with_icon_title">
                                    <b>User Business Scope</b>
                                    <p>
                                       <form action="{{ URL('/user_scope') }}" method="post" >
                                          {{ csrf_field() }}    <!-- token --> 

                                        <div class="row clearfix">
                                            
                                         @if(Session::has('username'))
                                        <?php 
                                        $user_email= Session::get('username');
                                       // echo $user_email;
                                        //echo"hello";
                                        
                                       
                                          $user1=DB::table('users')
                                        ->where('email',$user_email)
                                        ->first();
                                       
                                        if(isset($user)){
                    $user_id1=$user->id;
                     }
                     else{
                       $user_id1=1; 
                     }
                    

                                         ?>
                                          @endif
                                            <div class="col-sm-12" id="divName">
                                   <label for="division">Division:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div id="divName">

                                       </div>

                                        <select class="form-control show-tick" name="division_id" id="try" required="" onchange="getTerritory(this.value)">
                                        <option value="">Please Select Division</option>

                                        @foreach($division as $divisionName)
                                        <option value="{{ $divisionName->div_id }}">{{ $divisionName->div_name }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
                                </div>
                                     <label for="division">Territory:*</label>
                                    <div class="form-group" id="terriName">
                                        <div class="form-line" id="territory1">
                                       
                                        </div>
                                    </div>

                                    <!-- <label for="division">Point:*</label>
                                    <div class="form-group">
                                        <div class="form-line" id="points">
                                       
                                        </div>
                                    </div>-->

                                    <label for="division">Point:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" data-live-search="true" name="point_id" required="">
                                        <option value="">Please Select Point</option>
                                        @foreach($pointName as $point_name)
                                        <option value="{{ $point_name->point_id }}">{{ $point_name->point_name }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
                                   </div>
                               
                                <div class="text-center">
                                 <input type="hidden" name="user_id1" value="<?php if(isset($user_id1)) {echo $user_id1;} ?>">
                                  <input type="hidden" class="form-control" placeholder="" name="id" id="id2"  />
                            <button type="submit"  id="" class="btn btn-default btn-lg waves-effect m-r-20 center-block"><b>Save</b></button>
                          </div>
                                    
                                    </form>
                                    <!-- User  scope Starts -->
                          <?php 
                           $user_business_scope=DB::select("SELECT * FROM tbl_user_business_scope");
                         

                            ?>
                            <br>
                         <div class="table-responsive">
                          
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Division</th>
                        <th>Territory</th>
                        <th>Point</th>
                        
                        
                        <th class="">Action</th>
                    </tr>
                </thead>
                 <tbody>
                    
              @foreach($user_business_scope as $scope) 
                    <tr>
                        <?php 
                    $division=DB::select("select * from tbl_division where div_id='$scope->division_id'");
                    $terri_id=DB::select("select * from tbl_territory where id='$scope->territory_id'");
                    //dd( $terri_id);
                     foreach ($division as $div_id) {
                               # code...
                           
                               $div_name=$div_id->div_name;
                                
                            }
                    if( $terri_id){
                    foreach ($terri_id as $terri_no) {
                               # code...
                           
                               $terri_name=$terri_no->name;
                                
                            }
                        }
                        else
                        {
                          $terri_name='';  
                        }

                         
                        $user_name=DB::select(" select * from users where id='$scope->user_id'");
                        foreach ( $user_name as $name) {
                               # code...
                           
                                $name=$name->email;
                                
                            }
                    ?>
                        <th>{{$name}}</th>
                        <th>{{$div_name}}</th>
                        <th>{{$terri_name}}</th>
                        <th>{{$scope->point_name}}</th>
                        
                        <th>
                            <input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editUserScope('{{$scope->business_scope_id}}')" style="width: 70px;"">
                        </th>
                    </tr>

                    <!-- User  Details Ends -->
                   
                
                 
             @endforeach
               
             </tbody>
                
                <tfoot>
                    <tr>
                        <th>User Name</th>
                        <th>Division</th>
                        <th>Territory</th>
                        <th>Point</th>
                        <th class="">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
        <!-- User scope ends-->

                                    </p>
                                
                               </div>
                            
                        
                                
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End tab with icon-->
        
                </div>
            </div>
            <br>
               
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection