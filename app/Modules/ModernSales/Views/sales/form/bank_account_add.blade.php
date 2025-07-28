    @extends('ModernSales::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

             <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2>
                            Bank Account List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Bank Account List
                         </small>
                     </h2>
                    </div> 
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Account</button>
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
                    <form action="{{ URL('/mts-bank-account-add-process') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Bank Account</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6">
                                                <label for="name">A/C Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="A/C Name" name="name" id="name" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6"> 
                                                <label for="account_no">A/C No:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="A/C Number" name="account_no" id="account_no" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-4 col-md-4">
                                                <label for="division">Bank Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Bank Name" name="bank_name"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                <label for="division">Branch Name:</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Branch Name" name="branch_name"  autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-4 col-md-4">
                                                <label for="division">Short Code:</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Short Code" name="short_code"  autocomplete="off"/>
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
            </div>
        </div>
        <br>

        <div id="showHiddenDiv">
            <div class="table-responsive">
            <table class="table table-bordered dataTable table-hover">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>A/C Name </th>  
                        <th>A/C No</th> 
                        <th>Short Code</th> 
                        <th>Bank Name</th> 
                        <th>Branch Name</th> 
                        <th>Status</th> 
                        <th>Action</th>
                        @if(Auth::user()->user_type_id==1)
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($bankList) > 0)   
                    
                    @foreach($bankList as $row)  
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->accountname }}</td>  
                         <td>{{ $row->code }}</td> 
                         <td>{{ $row->shortcode }}</td> 
                        <td>{{ $row->bank_name }}</td>  
                        <td>{{ $row->branchname }}</td>   
                         <td> 
                            
                            @if($row->status==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td>  
                            
                            @if($row->status==0)
                            <a href="{{ URL('/mts-bank-account-active/'.$row->id.'/'. 0) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;" onclick="return confirm('Are you sure you want to In-Active?')">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/mts-bank-account-active/'.$row->id.'/'. 1) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;"onclick="return confirm('Are you sure you want to Active?')">Active</button>
                            </a>
                            @endif
                        </td>
                        @if(Auth::user()->user_type_id==1)
                        <td>  
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="bankEdit('{{ $row->id }}')" style="width: 70px;">

                            <a href="{{ URL('/mts-bank-account-delete/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                            </a>
                            
                           
                        </td>
                         @endif
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
        </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection

