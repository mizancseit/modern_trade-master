@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            COMPANY MANAGEMENT 
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
            Company Set Up
             <button type="button" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Company</button>
        </h2>
    </div>

    <div class="body">
         
         
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/company_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Company</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="SAP Code" name="sap_code" required="" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Company" name="name" required="" />
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Address" name="address" required="" />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Mobile" name="mobile" required="" />
                                        </div>
                                    </div>
                                   
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="T&T" name="tnt" required="" />
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
                        <th>SAP CODE</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Mobile</th>
                        <th>T&T</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>SAP CODE</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Mobile</th>
                        <th>T&T</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($company) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($company as $companySetup) 
                    <tr>
                         <th>{{$companySetup->id }}</th>
                        <th>{{$companySetup->sap_code }}</th>
                        <th>{{$companySetup->name }}</th>
                        <th>{{$companySetup->address}}</th>
                        <th>{{$companySetup->mobile }}</th>
                        <th>{{$companySetup->tnt }}</th>
                        <th><input type="button" name="company_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editCompany('{{ $companySetup->id }}')" style="width: 70px;""></th>
                        <th><input type="button" name="company_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteCompany('{{ $companySetup->id}}')" style="width: 70px; margin-top: 0px;"></th>
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
