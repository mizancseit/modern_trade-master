    @extends('eshop::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

             <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2>
                            Outlet List
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Outlet List
                         </small>
                     </h2>
                    </div> 
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Outlet</button>
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
                    <form action="{{ URL('/eshop-outlet-create') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Outlet</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6"> 
                                                 <label for="division">Customer:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select id="shop_type" name="customer_id" class="form-control" data-live-search="true">
                                                             
                                                              <option value="">-- Select Customer --</option> 
                                                             @foreach($resultCustomer as $cusList)
                                                            <option value="{{ $cusList->customer_id }}">{{ $cusList->name }}</option>
                                                            @endforeach  
                                                        </select>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Outlet Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Outlet Name" name="outlet_name"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-4">
                                                <label for="division">Mobile No:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Mobile No" name="mobile_no"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                <label for="division">SAP code:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="SAP code" name="sap_code"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4"> 
                                                 <label for="division">Shop Type:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select id="shop_type" name="shop_type" class="form-control" data-live-search="true">
                                                            <option value="">-- Shop Type --</option> 
                                                             @foreach($shopType as $shopType)
                                                            <option value="{{ $shopType->route_id }}">{{ $shopType->route_name }}</option>
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
         
         <div class="card">
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            
                            <select id="customer_id" name="customer_id" class="form-control" data-live-search="true">
                                <option value="">-- Select Customer --</option> 
                                  @foreach($resultCustomer as $row)
                                <option value="{{ $row->customer_id }}">{{ $row->name }}</option>
                                @endforeach    
                            </select>
                                
                        </div> 
                         
                        <div class="col-sm-4">
                            <select id="status" name="status" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Status --</option> 
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
                                                                                   
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="outletSearch()">Search</button>
                        </div>
                        <div class="col-sm-2">                        
                           <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </div>                                  
                </div>
            </div>

        <div id="showHiddenDiv">
            <div class="table-responsive">
            <table class="table table-bordered dataTable">
                <thead>
                    <tr>
                        <th>SL</th>  
                        <th>Customer Name</th> 
                        <th>Outlet Name</th> 
                        <th>Mobile No</th> 
                        <th>Address</th>  
                        <th>Type</th>  
                        <th>Status</th>  
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
                         <td>{{ $row->cname }}</td>  
                         <td>{{ $row->pname }}</td> 
                         <td>{{ $row->mobile }}</td> 
                         <td>{{ $row->address }}</td> 
                        <td>{{ $row->route_name }}</td> 
                         <td>                       
                            @if($row->status==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td> 
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="outletEdit('{{ $row->party_id }}')" style="width: 70px;">
                            
                            @if($row->status==0)
                            <a href="{{ URL('/eshop-outlet-active/'.$row->party_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/eshop-outlet-inactive/'.$row->party_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="8">No record found.</th>
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
