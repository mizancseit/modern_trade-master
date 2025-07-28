<!-- Default Size -->


<form action="{{ URL('mts-user-edit-process') }}" method="post" name="editForm">

    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit User Info</h4>
            </div>
            <div class="modal-body"> 
                <div class="row clearfix">
                   <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-6">
                            <label for="division">User Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="User Name" name="email" value="{{ $userList->email }}"  autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6"> 
                            <label for="division">Password:</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Password" name="password" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Full Name:</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Full Name" name="full_name" value="{{ $userList->display_name }}"  autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Emp ID:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Emp ID" name="emp_id" value="{{ $userList->employee_id }}" autocomplete="off"/>
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
                                        <option value="{{ $row->shot_code }}" @if ($userList->designation == $row->shot_code) {{ "selected" }} @endif>{{ $row->designation }}</option>
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
                                        <option value="{{ $userType->user_type_id }}" @if ($userList->user_type_id == $userType->user_type_id) {{ "selected" }} @endif>{{ $userType->user_type }}</option>
                                        @endforeach  
                                    </select>
                                </div>
                            </div>
                        </div> 
                    </div>
                     <div class="modal-footer">
                        <input type="hidden" id="id" name="id" value="{{ $userList->id }}">
                         <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
                         <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
                </div>
            </div> 
</div>
</div>
</form>


