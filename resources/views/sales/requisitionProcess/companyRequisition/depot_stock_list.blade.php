    @extends('sales.masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">

                        <h2>
                            Stock List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Target
                         </small>
                     </h2>
                    </div>
                    <div class="col-lg-2">
                        <a href="{{ url('demo/downloadExcel/stock_demo.csv') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Stock File</button>
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
                    <form action="{{ URL('depot/stock_products_upload') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Stock File Upload</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                       
                                           <div class="col-sm-6 col-md-4">
                                            <label for="qty">Stock File Upload : *</label>
                                            
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
                        <th>DATE</th>
                        <th>COM.CODE</th>
                        <th>COM.NAME</th>
                        <th>PLANT</th>
                        <th>MATERIAL NO</th>
                        <th>MATERIAL DESC</th>
                        <th>STOCK QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($stockList) > 0)   
                    @php
                    $i =1;
                    @endphp
                    @foreach($stockList as $stockList) 
                    <tr>
                        <td>{{$i++ }}</td>
                        <td>{{ date('d-m-Y', strtotime($stockList->dDate)) }}</td>
                        <td>{{$stockList->company_code }}</td>
                        <td>{{$stockList->company_name}}</td>
                        <td>{{$stockList->plant}}</td>
                        <td>{{$stockList->material_no}}</td>
                        <td>{{$stockList->material_desc}}</td>
                        <td>{{$stockList->stock_qty}}</td>
                        
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="8">No record found.</th>
                    </tr>
                    @endif     

                </tbody>
                <tbody>
                    <tfoot>
                        <tr>
                            <th>SL</th>
                            <th>DATE</th>
                            <th>COM.CODE</th>
                            <th>COM.NAME</th>
                            <th>PLANT</th>
                            <th>MATERIAL NO</th>
                            <th>MATERIAL DESC</th>
                            <th>STOCK QTY</th>
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
