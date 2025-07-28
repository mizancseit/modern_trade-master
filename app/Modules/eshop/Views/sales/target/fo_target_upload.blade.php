    @extends('eshop::masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">

                        <h2>
                            Target List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Target
                         </small>
                     </h2>
                    </div>
                    
                    <div class="col-lg-2">
                        <a href="{{ url('demo/downloadExcel/Target_file.xlsx') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal1">Add Target File</button>
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

        <form action="{{ URL('/admin/target-download') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}    <!-- token -->
            <div class="row clearfix">
                <div class="card">               
                    <div class="body">
                        <div class="row">                       
                            <div class="col-sm-2"> 
                                <select class="form-control show-tick" name="year" id="year" required="">
                                    @php
                                    $y= date('Y');
                                    for($i=2017;$i<=$y;$y--)
                                    {
                                    @endphp
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @php
                                    }
                                    @endphp
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <select class="form-control show-tick" name="month" id="month" required="">
                                    {{-- <option value="">Select Month</option> --}}
                                    @foreach($MonthList as $rowMonthKey => $rowMonth)
                                    <option value="{{ $rowMonthKey }}">{{ $rowMonth }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <select id="executive_id" name="executive_id" class="form-control show-tick" data-live-search="true" onchange="allOfficer(this.value)">
                                    <option value="">-- Select Executive--</option> 
                                    @foreach($executivelist as $row)
                                        <option value="{{ $row->id }}">{{$row->display_name }}</option>
                                    @endforeach                                                    
                                </select>
                            </div>

                            <div class="col-sm-3" id="officerDiv">
                                <select id="fos" name="fos" class="form-control show-tick" data-live-search="true">
                                    <option value="">-- Select Officer --</option> 
                                                                                  
                                </select>
                            </div>     
                             <div class="col-sm-2">
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="adminTargetSearch()">Search</button>
                            </div> 
                            
                        </div>

                        <div class="row"> 
                            
                            <div class="col-sm-2">
                                <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                            </div>
                            
                        </div>

                    </div>
                </div>
            </div>
        </form>


        <div class="row clearfix">


            <!-- #END# Exportable Table -->

            <div class="card">
               
                <div class="body">
                  <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
                    <form action="{{ URL('/modern_target_file_upload') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Target File Upload</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                       
                                           <div class="col-sm-6 col-md-4">
                                            <label for="qty">Target File Upload : *</label>
                                            
                                            </div>
                                            <div class="col-sm-6 col-md-8">
                                                <div class="form-group ">
                                                        <input type="file" class="form-control" name="imported-file" required="" />
                                                </div>
                                            </div>

                                         </div>
                                    </div>
                                </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">UPLOAD</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>

        <div id="showHiddenDiv">
        <div class="header">
            <h5>
                About {{ sizeof($targetList) }} results 
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>Year </th>
                        <th>Month</th>
                        <th>Customer Name</th>
                        <th>Customer Code</th>
                        <th>Officer</th>
                        <th>Executive</th>
                        <th>VALUE</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1; 
                    $totalValue =0;
                    @endphp
                    @if(sizeof($targetList) > 0)   
                    
                    @foreach($targetList as $targetData) 
                    @php 
                    $totalValue +=$targetData->value;

                    $executive  = DB::table('users') 
                    ->where('id', $targetData->executive_id)    
                    ->first();
                    @endphp
                    <tr>
                        <td>{{$i++ }}</td> 
                        <td>{{ $targetData->year }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, $targetData->month, 10)) }}</td> 
                        <td>{{ $targetData->cusname }}</td> 
                        <td>{{ $targetData->customer_id }}</td> 
                        <td>{{ $targetData->display_name }}</td> 
                       <td>@if(sizeof($executive)>0) {{ $executive->display_name }} @endif</td>
                        <td>{{ $targetData->value}}</td>
                        <td>
                             
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetEdit('{{ $targetData->id }}')" style="width: 70px;">

                            <a href="{{ URL('/eshop-target-delete/'.$targetData->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;" onclick="return confirm('Are you sure?')">Delete</button>
                            </a>
                             
                        </td>
                    </tr>


                    @endforeach
                    <tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">Total</th>
                            <th>{{ number_format($totalValue,2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody>
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
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection
