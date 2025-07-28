

@extends('eshop::masterPage')
@section('content')
<section class="content">
   <div class="container-fluid">
      <div class="block-header">
         <div class="row">
            <div class="col-lg-10">
               <h2> 
                  <small> 
                  <a href="{{ URL('/dashboard') }}"> Dashboard </a> / E-Shop Customar
                  </small>
               </h2>
            </div>
            <div class="col-lg-2">
               <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Customer</button>
            </div>
         </div>
      </div>
   </div>
   @if(Session::has('success'))
   <div class="alert alert-success">
      {{ Session::get('success') }}                        
   </div>
   @endif 
   <div class="row clearfix">
   <div class="card">
   <div class="body">
        <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
            <form action="{{ URL('/e-shop-customer-create') }}" method="POST">
                {{ csrf_field() }}  
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Customer</h4>
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
                                        <label for="division">Mobile No:</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Mobile No" name="mobile_no"  autocomplete="off"/>
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
                                                @foreach($resultFo as $resultFo)
                                                <option value="{{ $resultFo->id }}">{{ $resultFo->display_name }}</option>
                                                @endforeach  
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
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
                    </div>
                </div>
            </form>
        </div> 
        <div id="showHiddenDiv">
            <div class="table-responsive"> 
                <table class="table table-bordered dataTable table-hover">
                    <thead>
                        <tr>
                        <th>SL</th>  
                        <th>Customer Name</th> 
                        <th>Address</th> 
                        <th>Credit Limit</th>
                        <th>SAP Code</th>  
                        <th>Mobile</th> 
                        <th>Email</th> 
                        <th>Customer code</th> 
                        <th>Action</th>
                        <th></th>
                      
                        </tr>
                    </thead> 
                <tbody>
                    @php
                        $i =1;
                        @endphp
                        @if(sizeof($customerResult) > 0)    
                        @foreach($customerResult as $row)  
                            <tr>
                            <td>{{$i++ }}</td> 
                            <td>{{$row->name }}</td> 
                            <td>{{$row->address }}</td>
                            <td>{{ $row->credit_limit }}</td>  
                            <td>{{ $row->sap_code }}</td>  
                            <td>{{ $row->mobile }}</td> 
                            <td>{{ $row->email }}</td> 
                            <td>{{ $row->customer_code }}</td> 
                            <td>  
                                @if($row->status==0)
                            <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                                @else
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                                @endif
                            </td> 
                            <td> 
                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="eShopCustomerEdit('{{ $row->customer_id }}')" style="width: 70px;">
                                
                                @if($row->status==0)
                                <a href="{{ URL('/e-shop-customer-active/'.$row->customer_id) }}">
                                    <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                                </a>
                                @else
                                <a href="{{ URL('/e-shop-customer-inactive/'.$row->customer_id) }}">
                                    <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                                </a>
                                @endif
                            </td>
                            <!-- <td>  
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="eShopCustomerEdit('{{ $row->customer_id }}')" style="width: 70px;"> 
                            @if($row->status==0)
                            <a href="{{ URL('/e-shop-customer-inactive/'.$row->customer_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active {{$row->status}} </button>
                            </a>
                            @else
                            <a href="{{ URL('/e-shop-customer-active/'.$row->customer_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>  -->
                            </tr> 
                        @endforeach
                        @else
                        <tr>
                        <th colspan="9">No record found.</th>
                        </tr>
                    @endif     
                </tbody> 
                </table>
            </div>
        </div>  
   </div>
</section>
@endsection

