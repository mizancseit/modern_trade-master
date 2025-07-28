@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DIVISION SETUP 
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
            Division SetUp
            <button type="button" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add New Division</button>
        </h2>
    </div>

    <div class="body">
         
         
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/division_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add New Division</h4>
                        </div>
						
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Division Code" name="div_code" required="" />
                                        </div>
                                    </div>
									
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Division Name" name="div_name" required="" />
                                        </div>
                                    </div>
									
									
                                   
									<div class="form-group">
                                        <div class="form-line">
											<select class="form-control show-tick" name="div_status" required="">
												<option value="">Active/In-Active</option>
												<option value="0">ACTIVE</option>
												<option value="1">IN-ACTIVE</option>
											</select>
										</div>
                                    </div>
                                   
                                  
                                </div>
                            </div>
                        </div>
						<input type="hidden" name="id" id="id">
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
                        <th>Division Code</th>
                        <th>Division Name</th>
                        
						<!-- <th>Company Name</th> -->
                        
						<th>Status</th>
                        <th class="pull-right">Action</th>
						
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Division Code</th>
                        <th>Division Name</th>
                        
						<!-- <th>Company Name</th> -->
						
						<th>Status</th>
                        <th class="pull-right">Action</th>
						
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($divisionList) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($divisionList as $divisionRow) 
                    <tr>
                        <th>{{$divisionRow->div_id }}</th>
                        <th>{{$divisionRow->div_code }}</th>
                        <th>{{$divisionRow->div_name }}</th>
                    
						@if($divisionRow->div_status == 0)   
							 <th>Active</th>
						@else	
							 <th>In-Active</th>
						@endif	
				        
						<th><input type="button" name="division_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDivision('{{ $divisionRow->div_id }}')" style="width: 70px;">
                        <input type="button" name="division_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDivision('{{ $divisionRow->div_id}}')" style="width: 70px; margin-top: 0px;"></th>
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
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
