    @extends('sales.masterPage')
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
                        <a href="{{ url('demo/downloadExcel/Target_demo.csv') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Target File</button>
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
                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('/target_file_upload') }}" method="post" enctype="multipart/form-data">
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

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>EMPLOYEE ID</th>
						<th>FO NAME</th>
                        <th>YEAR</th>
                        <th>MONTH</th>
                        <th>CATEGORY NAME</th>
                        <th>QTY</th>
                        <th>AVG. VALUE</th>
                        <th>TOTAL VALUE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($targetList) > 0)   
                    @php
                    $i =1;
                    @endphp
                    @foreach($targetList as $targetData) 
                    <tr>
                        <td>{{$i++ }}</td>
                        <td>{{$targetData->employee_id }}</td>
						<td>{{$targetData->display_name }}</td>
                        <td>{{ date('Y', strtotime($targetData->start_date)) }}</td>
                        <td>{{ date('F', strtotime($targetData->end_date)) }}</td>
                        <td>{{$targetData->name}}</td>
                        <td>{{$targetData->qty}}</td>
                        <td>{{$targetData->avg_value}}</td>
                        <td>{{$targetData->total_value}}</td>
                        <td>

                            <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetEdit('{{ $targetData->id }}')" style="width: 70px;">
                            <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetDelete('{{ $targetData->id}}')" style="width: 70px; margin-top: 0px;">
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="10">No record found.</th>
                    </tr>
                    @endif     

                </tbody>
                <tbody>
                    <tfoot>
                        <tr>
                            <th>SL</th>
                            <th>EMPLOYEE ID</th>
							<th>FO NAME</th>
                            <th>YEAR</th>
                            <th>MONTH</th>
                            <th>CATEGORY NAME</th>
                            <th>QTY</th>
                            <th>AVG. VALUE</th>
                            <th>TOTAL VALUE</th>
                            <th>ACTION</th>
                        </tr>
                    </tfoot>
                    <tbody>

                    </table>
                </div>
            </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection
