@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            RETURN MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Return
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-2">
                        {{-- <div class="preloader">
                            <div class="spinner-layer pl-black">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        <div class="header">
                            <div class="row">
                                <div class="col-lg-10">
                                    <h2>ROUTE</h2>
                                </div>
                                <div class="col-lg-2">
                                    <div id="loading" style="display: none;">
                                        <div class="preloader pl-size-xs">
                                            <div class="spinner-layer pl-red-grey">
                                                <div class="circle-clipper left">
                                                    <div class="circle"></div>
                                                </div>
                                                <div class="circle-clipper right">
                                                    <div class="circle"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <span>Loading</span>                                        
                                    </div>
                                </div>
                            </div>                          
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="route" class="form-control show-tick" onchange="returnOnlyRoute()" data-live-search="true">
                                    <option value="">-- Please select route --</option>
                                    @foreach($routeResult as $routes)
                                    <option value="{{ $routes->route_id }}" @if($routeID!='') @if($routeID==$routes->route_id) selected="" @endif @endif> {{ $routes->rname }} </option>
                                    @endforeach                         
                                </select>
                            </form>
                        </div>
                    </div>

                    <div id="showHiddenDiv">
                        
                        {{-- Here Retailer List --}}
                        <div class="card">
                            <div class="header">
                                <h2>
                                    Retailer List
                                </h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Retailer</th>
                                                <th>Return</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>SL</th>
                                                <th>Retailer</th>
                                                <th>Return</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        @if(sizeof($resultRetailer) > 0)   
                                            @php
                                            $serial =1;
                                            @endphp

                                            @foreach($resultRetailer as $retailers)

                                            @php
                                            $resultRetailer = DB::table('tbl_visit_return_only')
                                                ->select('id','retailerid','foid','routeid','date','status')                       
                                                ->where('date', date('Y-m-d'))
                                                ->where('foid', Auth::user()->id)
                                                ->where('retailerid', $retailers->retailer_id)
                                                ->orderBy('id','DESC')                    
                                                ->first();

                                            $checkRetailers = '';
                                            $checkStatus = '';
                                            if (sizeof($resultRetailer)>0) 
                                            {
                                                $checkRetailers = $resultRetailer->retailerid;
                                                $checkStatus    = $resultRetailer->status;
                                            }
                                            
                                            @endphp 
                                            <tr>
                                                <th>{{ $serial }}</th>
                                                <th>{{ $retailers->name }}</th>
                                                @if($checkRetailers!=$retailers->retailer_id)
                                                <th><a href="{{ URL('/return-only-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Return Collect </a></th>
                                                <th></th>
                                                @else
                                                <th>
                                                @if($checkStatus!=3)
                                                    <a href="{{ URL('/return-only-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Order </a>
                                                @endif
                                                </th>
                                                                      
                                                <th>
                                                    @if($checkStatus==3)
                                                    Collected
                                                    @elseif($checkStatus==2)
                                                    Visit
                                                    @elseif($checkStatus==1)
                                                    Non-visit
                                                    @endif

                                                </th>
                                                @endif
                                            </tr>
                                            @php
                                            $serial++;
                                            @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <th colspan="4">No record found.</th>
                                            </tr>
                                        @endif    
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection