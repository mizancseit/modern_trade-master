    @extends('ModernSales::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

             <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2>
                            Supervisor List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Supervisor
                         </small>
                     </h2>
                    </div> 
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Supervisor</button>
                    </div>
                     
                </div>

            </div>

         @if(Session::has('success'))
         <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}                        
        </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

       

        <div class="row clearfix">


            <!-- #END# Exportable Table -->

            <div class="card">
               
                <div class="body">
                  <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
                    <form action="{{ URL('/supervisor-create') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Supervisor</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Customer Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Customer Name" name="customer_name"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Mobile No:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Mobile No" name="mobile_no"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-4">
                                                <label for="division">Customer code:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Customer code" name="customer_code"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <label for="division">SAP code:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="SAP code" name="sap_code"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4"> 
                                                <label for="division">Credit limit:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Credit limit" name="credit_limit" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12">  
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="division">Define Officer:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                       <select id="executive_id" name="executive_id" class="form-control" data-live-search="true">
                                                            <option value="">-- Select Officer --</option> 
                                                             
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6"> 
                                                 <label for="division">User Type:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select id="shop_type" name="shop_type" class="form-control" data-live-search="true">
                                                            <option value="">-- User Type --</option> 
                                                             @foreach($user_type as $userType)
                                                            <option value="{{ $userType->user_type_id }}">{{ $userType->user_type }}</option>
                                                            @endforeach  
                                                        </select>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12"> 
                                                <label for="division">Address:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Address" name="address" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">Save</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>

        <div id="showHiddenDiv">
            <div class="table-responsive">
            <table class="table table-bordered dataTable">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>Customer Code </th>
                         
                        <th>Customer Name</th> 
                        <th>Mobile No</th> 
                        <th>Address</th> 
                        <th>Credit Limit</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($resultcus) > 0)   
                    
                    @foreach($resultcus as $row) 
                    
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->sap_code }}</td>  
                         <td>{{ $row->name }}</td> 
                         <td>{{ $row->mobile }}</td> 
                         <td>{{ $row->address }}</td> 
                        <td>{{ $row->credit_limit }}</td> 
                         
                        <td> 
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="customerEdit('{{ $row->customer_id }}')" style="width: 70px;">
                            
                            @if($row->status==0)
                            <a href="{{ URL('/mts-customer-active/'.$row->customer_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/mts-customer-inactive/'.$row->customer_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Inactive</button>
                            </a>
                            @endif
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                    @endif     

                </tbody> 
                    </table>
                </div>
                </div>
            </div>
        </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection
