@extends('ModernSales::masterPage')
@section('content')
        <section class="content">
            <div class="container-fluid">
                <div class="block-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>
                                Define Supervisor
                                <small> 
                                   <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Supervisor List
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
                <div class="body"> 
                    <button type="button" id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Supervisor</button>
                      <br>
                    <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                        <form action="{{ URL('mst-supervisor_save') }}" method="post">
                            {{ csrf_field() }}    <!-- token -->
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                            <div class="modal-header" style="background-color: #A62B7F">
                                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Supervisor</h4>
                            </div>
                            <div class="modal-body">

                                <div class="row clearfix">

                                    <div class="col-sm-12 col-md-12">
                                        <div class="row clearfix">
                                            <div class="col-sm-12 col-md-6">
                                                <label for="supervisorType">SUPERVISOR TYPE :*</label>
                                                <div class="form-group">
                                                    <div class="form-line"> 
                                                        <select class="form-control show-tick" name="supervisor_type" required="" onchange="getSupervisor(this.value)" data-live-search='true'>
                                                            <option value="">Select Supervisor Type</option>
                                                            @foreach($user_type as $usertype)
                                                            <option value="{{ $usertype->user_type_id }}">{{ $usertype->user_type }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label for="supervisor">SUPERVISOR NAME :*</label>
                                                <div class="form-group">
                                                    <div class="form-line" id="supervisor">
                                                        <select class="form-control show-tick" name="supervisor_id" required="" data-live-search='true'> 
                                                            <option value="">Select Name</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>

                                        <div class="row clearfix">                          
                                            <div class="col-sm-12 col-md-6">
                                                <label for="management_id">Management :*</label>
                                                <div class="form-group">
                                                    <div class="form-line" style="height: auto;">
                                                        <select class="form-control show-tick" name="management_id" id="management_id" required="">
                                                            <option value="">Select Management</option>
                                                            @foreach($management as $managemen)
                                                            <option value="{{ $managemen->id }}">{{ $managemen->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label for="manager_id">Manager :*</label>
                                                <div class="form-group">
                                                    <div class="form-line">

                                                        <select class="form-control show-tick" name="manager_id" required="" id="manager_id" >
                                                            <option value="">Select Manager</option>
                                                            @foreach($managers as $manage)
                                                            <option value="{{ $manage->id }}">{{ $manage->display_name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row clearfix">
                                            <div class="col-sm-12 col-md-6">
                                                <label for="executive_id">Executive :*</label>
                                                <div class="form-group">
                                                    <div class="form-line" id="executive_id" style="height: auto;">
                                                        <select class="form-control show-tick" required="" name="executive_id">
                                                            <option value="">Select Executive</option>
                                                            @foreach($executive as $execu)
                                                            <option value="{{ $execu->id }}">{{ $execu->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label for="officer_id">Office :*</label>
                                                <div class="form-group">
                                                    <div class="form-line" id="officer_id" style="height: auto;">
                                                        <select class="form-control show-tick" required="" name="officer_id">
                                                            <option value="">Select Office</option>
                                                            @foreach($officer as $offic)
                                                            <option value="{{ $offic->id }}">{{ $offic->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>                                   
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    <br>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Supervisor Type</th>
                    <th>Supervisor Name</th>
                    <th>Management </th>
                    <th>Manager </th>
                    <th>Executive</th>
                    <th>Office </th> 
                    <th>Action </th>  
                </tr>
            </thead> 
                <tbody> 
                    @if($data)
                    @foreach($data as $info)
                    <tr> 
                        <td>{{$info->hierarchy_id}}</td> 
                        <td>@if($info->supervisortype){{$info->supervisortype->user_type}} @endif</td>
                        <td>@if($info->supervisor){{$info->supervisor->display_name }} @endif</td> 
                        <td>@if($info->namagement){{$info->namagement->display_name}} @endif</td> 
                        <td>@if($info->manager) {{$info->manager->display_name}} @endif</td>    
                        <td>@if($info->executive) {{$info->executive->display_name}} @endif</td>    
                        <td>@if($info->officer) {{$info->officer->display_name}} @endif</td> 
                        <td>
                            <a href="" onclick="supervisorEdit({{$info->hierarchy_id}})" class="btn btn-warning" id="editsupervisor" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#editModal" data-bind="{{$info->hierarchy_id}}">Edit</a> 

                            <a onclick="return confirm('Are you sure you want to delete this item?');" href="{{ URL('mst-supervisor_delete',$info->hierarchy_id) }}" class="btn btn-danger">Delete</a> </td>    
                    </tr>
                    @endforeach
                    @endif
                <tfoot> 
                    <th>SL</th>
                    <th>Supervisor Type</th>
                    <th>Supervisor Name</th>
                    <th>Management </th>
                    <th>Manager </th>
                    <th>Executive</th>
                    <th>Office</th>
                    <th>Office</th>                   
                </tfoot>
                <tbody>

            </table>
        </div>
    </div>
    </div>
    <!-- #END# Exportable Table -->
    </div>
    </section> 


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <form action="{{ URL('mst-supervisor_edit/') }}" method="post" id="editFrom">
            {{ csrf_field() }}    <!-- token -->
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #A62B7F">
                        <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Supervisor</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row clearfix" id="editData">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
                </div>
            </div>
        </form>
    </div>



    <script type="text/javascript">
        function getSupervisor(slID)
        { 
            $.ajax({
                method: "GET",
                url: '{{url('/mst-get_supervisor_list')}}',
                data: {id: slID}
            })
            .done(function (response){  
                $('#supervisor').html(response);  
                $(".form-control.show-tick").selectpicker('render');
            });            
        } 

        function supervisorEdit(id)
        {  
            var action = '{{ URL('mst-supervisor_edit/')}}/'+id;
            $("#editFrom").attr("action", action);
            $.ajax({
                method: "GET",
                url: '{{url('/mst-get_supervisor')}}/'+id, 
            })
            .done(function (response)
            { 
                $('#editData').html('');  
                $('#editData').append(response);                
            });      
        }

    </script>
@endsection
