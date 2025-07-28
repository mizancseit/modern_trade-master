@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            POINT INFORMATION 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Point
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
            Point Set Up
        </h2>
    </div>

    <div class="body">
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Point</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/point_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Point</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                   <label for="division">Division:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="point_division" required="" onchange="getTerritory(this.value)">
                                        <option value="">Please Select Division</option>
                                        @foreach($division as $divisionName)
                                        <option value="{{ $divisionName->div_id }}">{{ $divisionName->div_name }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
                                	
									
									<label for="division">Territory:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Territory</option>
                                        @foreach($territoryList as $rowTerritory)
                                        <option value="{{ $rowTerritory->id }}">{{ $rowTerritory->name }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
                                    
									<label for="division">Name Of the Point:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Point Name" name="point_name" required="" />
                                                 
                                         
                                        </div>
                                    </div>
									
									<label for="division">Company:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="company_id" required="">
                                        <option value="">Please Select Company</option>
                                        @foreach($companyList as $rowCompany)
                                        <option value="{{ $rowCompany->global_company_id }}">{{ $rowCompany->global_company_name }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Business Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="business_type_id" required="">
                                        <option value="">Select Business Type</option>
                                        @foreach($businessTypeList as $rowBusiness)
                                        <option value="{{ $rowBusiness->business_type_id }}">{{ $rowBusiness->business_type }}</option>
                                         @endforeach
                                        </select>

                                        </div>
                                    </div>
                                      
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
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
                        <th>Sl</th>
                        <th>Division</th>
                        <th>Territory name</th>
                        <th>Company name</th>
                        <th>Business type</th>
                        <th>Point name</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>
              @if(sizeof($point) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($point as $pointSetup) 
                    <tr>
                        <th>{{$serial}}</th>
                        <th>{{$pointSetup->div_name }}</th>
                        <th>{{$pointSetup->territory_name}}</th>
                        <th>{{$pointSetup->company_name}}</th>
                        <th>{{$pointSetup->business_type}}</th>
                        <th>{{$pointSetup->point_name}}</th>
                        <th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editPoint('{{ $pointSetup->point_id }}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deletePoint('{{ $pointSetup->point_id}}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                    <tr>
					    <th>Sl</th>
                        <th>Division</th>
                        <th>Territory name</th>
                         <th>Company name</th>
						 <th>Business type</th>
						<th>Point name</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>-->
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
