@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">

                        <h2>
                           Retailer Balance List
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/dashboard') }}"> Depot </a> 
                            </small>
                        </h2>
                    </div>
					<!--
                    <div class="col-lg-2">
                        <a href="{{ url('demo/downloadExcel/retailer_balance.csv') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Balance File</button>
                    </div> -->
                     
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
                    <form action="{{ URL('/balance_file_upload') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Balance Upload</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                       
                                           <div class="col-sm-6 col-md-4">
                                            <label for="qty">Balance Upload : *</label>
                                            
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
                            <input type="hidden" id="point_id" name="point_id" value="">
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
                                <select id="route" class="form-control show-tick" onchange="retailerListByRoute()" data-live-search="true">
                                    <option value="">-- Please Select Route --</option>
                                    @foreach($resultRoute as $rowRoutes)
                                    <option value="{{ $rowRoutes->route_id }}">{{ $rowRoutes->rname }}</option>
                                    @endforeach                           
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

           
		    <form action="{{ URL('/update_retailer_balance') }}" method="POST">
                
				{{ csrf_field() }}    <!-- token -->
				
				<div id="showHiddenDiv">                        
                    {{-- Here Retailer List --}}
                </div>
			
			</form> 	
          

        </div>
    </section>
@endsection