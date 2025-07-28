        @extends('sales.masterPage')
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

                      <button type="button"  id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Supervisor</button>
                      <br>
                      <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                        <form action="{{ URL('supervisor_save') }}" method="post">
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

                                                        <select class="form-control show-tick" name="supervisorType" required="" onchange="getSupervisor(this.value)">
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
                                                        <select class="form-control show-tick" name="supervisor" required="">
                                                            <option value="">Select Name</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row clearfix">
                                             <div class="col-sm-12 col-md-6">
                                                <label for="category">DIVISION :*</label>
                                                <div class="form-group">
                                                    <div class="form-line">

                                                        <select class="form-control show-tick" name="division" required="" onchange="getUser(this.value)">
                                                            <option value="">Select division</option>
                                                            @foreach($division as $division)
                                                            <option value="{{ $division->div_id }}">{{ $division->div_name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label for="point">POINT NAME :*</label>
                                                <div class="form-group">
                                                    <div class="form-line" id="point" style="height: auto;">
                                                        <select class="form-control show-tick" required="">
                                                            <option value="">Select Point</option>
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
                    <th>Supervisor</th>
                    <th>Type</th>
                    <th>Division</th>
                    <th>Point</th>
                    <th>Action</th>
                    
                </tr>
            </thead>
            <tbody>
                @if(sizeof($supervisorList) > 0)   
                @php
                $i =1;
                @endphp

                @foreach($supervisorList as $supervisorList) 
               

                <tr>
                    <td>{{$i++ }}</td>
                    <td>{{$supervisorList->display_name}}</td>
                    <td>{{$supervisorList->user_type}}</td>
                    <td>{{$supervisorList->div_name}}</td>
                    <td>{{$supervisorList->point_name}}</td>
                    <td>
                 
                    <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editSpecialSku('{{ $supervisorList->iId }}')" style="width: 70px;">
                    <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteSupervisor('{{ $supervisorList->iId}}')" style="width: 70px; margin-top: 0px;">
                </td>
                    
                    
                </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="6">No record found.</th>
                    </tr>
                    @endif     

                </tbody>
                <tbody>
                <tfoot>
                    <th>SL</th>
                    <th>Supervisor</th>
                    <th>Type</th>
                    <th>Division</th>
                    <th>Point</th>
                    <th>Action</th>
                    
                </tfoot>
                <tbody>

            </table>
        </div>
    </div>
    </div>
    <!-- #END# Exportable Table -->
    </div>
    </section>
    @endsection
