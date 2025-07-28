@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            PRODUCT CATEGORY INFORMATION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Product Category
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
            Product Category Set Up
        </h2>
    </div>

    <div class="body">
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Product Category</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/proCategory_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Product Category</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">

                                <div class="col-sm-12">
                                    <label for="division">Code:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Code" name="g_code" id="g_code" required="" />
                                                 
                                         </div>
                                    </div>
                                     <label for="division">Name:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Name" name="name" id="name" required="" />
                                                 
                                         </div>
                                    </div>
                                   <label for="division">Type:*</label>
                                     
                                    <div class="form-group" id="type1">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="g_name" id="g_name" required="">
                                           
                                        <option value="">Please Select Type</option>
                                        

                                        <option value="Accessories">Accessories</option>
                                        <option value="FAN">FAN</option>
                                        <option value="Lighting"> Lighting</option>
                                       
                                        
                                        </select>

                                        </div>
                                    </div>

                                    <label for="division">Company:*</label>
                                    <div class="form-group" id="cid">
                                        <div class="form-line">
                                        <select class="form-control" name="company_id" id="company_id" required="">
                                            <option value="">Please Select Company</option>
                                            @foreach($tbl_company as $company)
                                                <option value="{{$company->global_company_id}}">{{$company->global_company_name}}</option>
                                            @endforeach
                                        </select>
                                        </div>
                                    </div>
                                   
                                   <label for="division">Avg. Price:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Avg. Price" name="avg_price" id="avg_price" required="" />
                                                 
                                         </div>
                                    </div>
                                      
                                    
                                </div>
                            </div>
                        </div>
                           <input type="hidden" class="form-control" placeholder="" name="id" id="id"  />
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                            <button type="button" onclick="modelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <br>
               <div class="body">
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Group Code</th>
                        <th>Group Name</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Avg Price</th>
                         <th>User</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>
             @if(sizeof($categoryDetails) > 0)   
                    @php
                    $serial =1;
                    @endphp


                    @foreach($categoryDetails as $proDetails) 
                    <tr>
                        <th>{{$proDetails->g_code}}</th>
                        <th>{{$proDetails->name}}</th>
                        <th>{{$proDetails->g_name}}</th>
                        <?php $compName=DB::select("select * from tbl_global_company where global_company_id=$proDetails->global_company_id");?>
                        <th><?php foreach ($compName as $comp){echo $comp->global_company_name;}?></th>
                        <th>{{$proDetails->avg_price}}</th>
                        <th>{{$proDetails->user}}</th>
                        <th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editProCategory('{{$proDetails->id}}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteProcategory('{{$proDetails->id}}')" style="width: 70px; margin-top: 0px;"></th>
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
                       <th>Group Code</th>
                        <th>Group Name</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Avg Price</th>
                         <th>User</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
               
            </table>
        </div>
    </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection