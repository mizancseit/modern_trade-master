@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            NEW RETAILE RMANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div>                    
                </div>                
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">
                <div class="header">
                <h2>
                NEW RETAILER LIST 
                </h2>
                </div>

                <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                <tr>
                <th>SL</th>
                <th>Fo Name</th>
                <th>Point</th>
                <th>Route</th>
                <th>Retailer</th>
                <th>Status</th>                                
                </tr>
                </thead>
                <tfoot>
                <tr>
                <th>SL</th>
                <th>Fo Name</th>
                <th>Point</th>
                <th>Route</th>
                <th>Retailer</th>
                <th>Status</th>                        
                </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultRetailer) > 0)   
                @php
                $serial =1;
                $status ='';
                @endphp

                @foreach($resultRetailer as $retailers)

                <tr>
                <th>{{ $serial }}</th>
                <th>{{ $retailers->display_name }} <br /> {{ $retailers->cell_phone }}</th>                        
                <th>{{ $retailers->point_name }}</th>                        
                <th>{{ $retailers->rname }}</th>                        
                <th>{{ $retailers->name }}</th>                        
                <th>
                    @if($retailers->status==0)
                    <button type="button" class="btn bg-green waves-effect" title="Click To Inactive" data-toggle="modal" data-target="#retilerActiveOrInactive">Active</button>
                    @php
                     $status =1 
                    @endphp                  
                    @else
                    <a href="{{ URL('/admin/retailer-done/'.$retailers->retailer_id) }}">
                    <button type="button" class="btn bg-red waves-effect" title="Click To Active">Inactive</button>
                    </a>                    
                    <a href="{{ URL('/retailer-req-delete/'.$retailers->retailer_id)}}" onclick="return  confirm('Are you sure to delete this Retailer?');">                   
                    <button type="button" class="btn bg-red waves-effect" title="Click To Delete" >Delete</button>
                    </a>
                    @php
                     $status =0 
                    @endphp 
                    @endif

                    <input type="hidden" name="status" id="status" value="{{$status}}">
                </th>
                                                        
                </tr>
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                <th colspan="6">No record found.</th>
                </tr>
                @endif 

                <input type="hidden" id="mVal" value="">   

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

<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                <h4 class="modal-title" id="myModalLabel" >Delete</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;">
                <p><h4>Are you sure?</h4></p>
                <p>You will not be able to recover this imaginary file!</p>
                <p class="debug-url"></p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger btn-ok" onclick="deleteReq()">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection