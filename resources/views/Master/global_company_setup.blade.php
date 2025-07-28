@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            GLOBAL COMPANY MANAGEMENT 
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
            Global Company Set Up
             <button type="button" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Global Company</button>
        </h2>
    </div>

    <div class="body">
         
         
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/globalCompany_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Gloabl Company</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company" name="global_company_name" required="" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Owner" name="global_company_owner" required="" />
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Email" name="global_company_email" required="" />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Phone" name="global_company_phone" required="" />
                                        </div>
                                    </div>
									
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Global Company Address" name="global_company_address" required="" />
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
            <table class="table table-bordered table-striped table-hover dataTable js-exportable ">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Global Company Name</th>
                        <th>Company Owner</th>
                        <th>Company Email</th>
                        <th>Company Phone</th>
                        <th>Company Address</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
						<th>SL</th>
                        <th>Global Company Name</th>
                        <th>Company Owner</th>
                        <th>Company Email</th>
                        <th>Company Phone</th>
                        <th>Company Address</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($GlobalCompanyList) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($GlobalCompanyList as $CompanyList) 
                    <tr>
                        <th>{{$serial }}</th>
                        <th>{{$CompanyList->global_company_name }}</th>
                        <th>{{$CompanyList->global_company_owner }}</th>
                        <th>{{$CompanyList->global_company_email}}</th>
                        <th>{{$CompanyList->global_company_phone }}</th>
                        <th>{{$CompanyList->global_company_address }}</th>
                        
						<th><input type="button" name="company_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editGlobalCompany('{{ $CompanyList->global_company_id }}')" style="width: 70px;""></th>
                        <th><input type="button" name="company_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteGlobalCompany('{{ $CompanyList->global_company_id}}')" style="width: 70px; margin-top: 0px;"></th>
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
