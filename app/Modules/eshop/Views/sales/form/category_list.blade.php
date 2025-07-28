    @extends('eshop::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

             <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2> 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Category List
                         </small>
                     </h2>
                    </div> 
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Category</button>
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
        @if ($errors->any())
            <div class="alert alert-danger">
                 
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }} <br></p>
                    @endforeach
                 
            </div>
        @endif 
       

        <div class="row clearfix">


            <!-- #END# Exportable Table -->

            <div class="card">
               
                <div class="body">
                  <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
                    <form action="{{ URL('/eshop-category-create') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Category</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                        <div class="col-sm-12 col-md-12"> 
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="division">Channel:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                       <select id="channel" name="channel" class="form-control" data-live-search="true" required="">
                                                            <option value="">-- Select Channel --</option> 
                                                              @foreach($resultChannel as $row)
                                                            <option value="{{ $row->business_type_id }}">{{ $row->business_type }}</option>
                                                            @endforeach    
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Company Code:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Company Code" name="company_code"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Category Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Product Name" name="name" autocomplete="off"/>
                                                    </div>
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
                <div class="header">
                    <h2>Products List</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            
                                    <select id="channel1" name="channel1" class="form-control" data-live-search="true" onchange="allCategory(this.value)">
                                        <option value="">-- Select Channel --</option> 
                                          @foreach($resultChannel as $row)
                                        <option value="{{ $row->business_type_id }}">{{ $row->business_type }}</option>
                                        @endforeach    
                                    </select>
                                
                        </div> 
                        <div class="col-sm-3">
                            <select id="status" name="status" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Status --</option> 
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
                                                                                   
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="categorySearch()">Search</button>
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
                        <th>business_type </th>
                        <th>Company Code </th>
                        <th>Category Name </th>     
                        <th>Status</th>  
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($resultProduct) > 0)   
                    
                    @foreach($resultProduct as $row)  
                    
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->business_type }}</td> 
                         <td>{{ $row->g_code }}</td>  
                         <td>{{ $row->cname }}</td>   
                         <td>                       
                            @if($row->status==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td>
                            
                            <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="categoryEdit('{{ $row->id }}')" style="width: 70px;">
                           
                            @if($row->status==0)
                            <a href="{{ URL('/eshop-category-active/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/eshop-category-inactive/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>
                    </tr> 
                    @endforeach
                    @else
                    <tr>
                        <th colspan="10">No record found.</th>
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

