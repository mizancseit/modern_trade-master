@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            ROUTE INFORMATION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Route
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
            Route Set Up
        </h2>
    </div>

    <div class="body">
        
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal" onclick="hideInput()">Add Route</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/route_process') }}" method="post">
                {{ csrf_field() }}    <!-- token --> 
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Route</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                   <label for="point">Point:*</label>
                                    <div class="form-group">
                                        <div class="form-line"> 
 
                                            
                                           
                                        <input type="text" id="point_name" name="" disabled="" >
                                        
                                        
                                        <select class="form-control show-tick" data-live-search="true" name="point_id" required="" ">
                                        <option value="" id="point">Please Select Point</option>
                                       
                                       
                                        </select>

                                        </div>
                                    </div>
                                    
                                     <label for="division">Name Of the Route:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Route Name" name="rname" id="rname" required="" />
                                                 
                                        </div>
                                    </div>
                                    <label for="division">Route Details:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Route Details" name="details" id="details" />
                                                 
                                        </div>
                                    </div>  

                                    
                                    <input type="hidden" class="form-control" placeholder="Route Id" name="route_id" id="route_id" value="" />
                                     

 
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
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
                        <th>Point Name</th>
                        <th>Route Name</th>
                        <th>Route Details</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                 <tbody>
             
                    <tr>
                         <th></th>
                        <th></th>
                        <th></th>
                        <th><input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editRoute()" style="width: 70px;"">
                        <input type="button" name="route_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteRoute()" style="width: 70px; margin-top: 0px;"></th>
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
                        <th>Point Name</th>
                        <th>Route Name</th>
                        <th>Route Details</th>
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
