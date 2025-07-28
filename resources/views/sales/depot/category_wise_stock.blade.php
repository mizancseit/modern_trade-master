@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">

                        <h2>
                           Depot Inventory
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Depot </a> 
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-2">
                        <a href="{{ url('demo/downloadExcel/stock_inventory.csv') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Inventory File</button>
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
               
                <div class="body">
                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('/inventory_file_upload') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Inventory Upload</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                       
                                           <div class="col-sm-6 col-md-4">
                                            <label for="qty">Inventory Upload : *</label>
                                            
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
                            <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                            <input type="hidden" id="inOut" name="inOut" value="{{ $inOut }}">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">UPLOAD</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="depotProducts()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->g_code.' : '.$categories->name }}</option>
                                    @endforeach                           
                                </select>
                                {{-- <select class="form-control show-tick">
                                    <option value="">-- Please select subcategory--</option>             
                                </select> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/add_to_inventory') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                <input type="hidden" id="inOut" name="inOut" value="{{ $inOut }}">
                <div id="showHiddenDiv">                        
                    {{-- Here Product List --}}
                </div>
            </form> 

        </div>
    </section>
@endsection