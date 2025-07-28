    @extends('ModernSales::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

             <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2>
                            Customer List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / User List
                         </small>
                     </h2>
                    </div> 
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add User</button>
                    </div>
                     
                </div>

            </div>

         @if(Session::has('success'))
         <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}                        
        </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

       

        <div class="row clearfix">


            <!-- #END# Exportable Table -->

            <div class="card">
               
                <div class="body">
                  <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
                    <form action="{{ URL('/mts-user-add-process') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add User</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">User Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="User Name" name="email"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="division">Password:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Password" name="password" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Full Name:</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Full Name" name="full_name"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Emp ID:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Emp ID" name="emp_id"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                        <div class="col-sm-12 col-md-12"> 
                                             
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="division">Designation:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                       <select id="designation" name="designation" class="form-control" data-live-search="true">
                                                            <option value="">-- Select Designation --</option> 
                                                             @foreach($designation as $row)
                                                            <option value="{{ $row->shot_code }}">{{ $row->designation }}</option>
                                                            @endforeach  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="division">User Type:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                       <select id="user_type" name="user_type" class="form-control" data-live-search="true">
                                                            <option value="">-- Select Type --</option> 
                                                             @foreach($userType as $userType)
                                                            <option value="{{ $userType->user_type_id }}">{{ $userType->user_type }}</option>
                                                            @endforeach  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                         <div class="modal-footer">
                                            <button type="submit" name="submit" class="btn btn-link waves-effect">Save</button>
                                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                                        </div>
                                    </div>

                                </div>
                        
                    </form>
                </div>
            </div>
        </div>
        <br>

        <div id="showHiddenDiv">
            <div class="table-responsive">
            <table class="table table-bordered dataTable table-hover">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>User Name </th>
                         
                        <th>Full Name</th> 
                        <th>Emp ID</th> 
                        <th>Designation</th> 
                        <th>User Type</th> 
                        <th>Status</th> 
                        <th>Action</th>
                        @if(Auth::user()->user_type_id==1)
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($userList) > 0)   
                    
                    @foreach($userList as $row) 
                    
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->email }}</td>  
                         <td>{{ $row->display_name }}</td> 
                         <td>{{ $row->employee_id }}</td> 
                         <td>{{ $row->designation }}</td> 
                        <td>{{ $row->user_type }}</td>  
                         <td> 
                            
                            @if($row->is_active==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td> 
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="userEdit('{{ $row->id }}')" style="width: 70px;">
                            
                            @if($row->is_active==0)
                            <a href="{{ URL('/mts-user-active/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/mts-user-inactive/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>
                        @if(Auth::user()->user_type_id==1)
                        <td>  
                            <a href="{{ URL('/mts-user-delete/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Delete</button>
                            </a>
                            
                           
                        </td>
                         @endif
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="9">No record found.</th>
                    </tr>
                    @endif     

                </tbody> 
                    </table>
                </div>
                </div>
            </div>
        </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection

