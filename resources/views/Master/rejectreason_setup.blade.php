@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            REJECT REASON SETUP 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Company
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
            Reject Reason SetUp
            <button type="button" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add New Reason</button>
        </h2>
    </div>

    <div class="body">
         
         
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/rejectreason_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add New Reason</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Reason" name="reason" required="" />
                                        </div>
                                    </div>
                                      
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="reason_type" required="">
												<option value="">Select Reason Type</option>
												<option value="0">VISIT</option>
												<option value="1">NON-VISIT</option>
											</select>
										</div>
                                    </div>
                                   
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="reason_status" required="">
												<option value="">Active/In-Active</option>
												<option value="0">ACTIVE</option>
												<option value="1">IN-ACTIVE</option>
											</select>
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
                        <th>Reject Reason</th>
                        <th>Reason Type</th>
                        <th>Status</th>
                        <th class="pull-right">Action</th>
						
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                       <th>SL</th>
                        <th>Reject Reason</th>
                        <th>Reason Type</th>
                        <th>Status</th>
                        <th class="pull-right">Action</th>
						
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($rejectreasonList) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($rejectreasonList as $rejectreasonRow) 
                    <tr>
                        <th>{{$rejectreasonRow->id }}</th>
                        <th>{{$rejectreasonRow->reason }}</th>
                        
						@if($rejectreasonRow->type == 1)   
							 <th>Visit</th>
						@else	
							 <th>Non-Visit</th>
						@endif

						@if($rejectreasonRow->reason_status == 0)   
							 <th>Active</th>
						@else	
							 <th>In-Active</th>
						@endif	
				        
						<th><!--<input type="button" name="rejreason_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editReason('{{ $rejectreasonRow->id }}')" style="width: 70px;">-->
                        <input type="button" name="rejreason_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteReason('{{ $rejectreasonRow->id}}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>
                   
                 
                @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="5">No record found.</th>
                    </tr>
                @endif     
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
