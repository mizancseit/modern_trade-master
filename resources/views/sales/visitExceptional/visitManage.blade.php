@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            VISIT MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Visit
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
                                <select id="route" class="form-control show-tick" onchange="allRetailerExc()" data-live-search="true">
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
                                    Visit Data
                                </h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Retailer</th>
                                                <th>Address</th>
                                                <th colspan="3" style="text-align: center;">Order</th>
                                                <th>Visit</th>
                                                <th>Non-visit</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>SL</th>
                                                <th>Retailer</th>
                                                <th>Address</th>
                                                <th colspan="3" style="text-align: center;">Order</th>
                                                <th>Visit</th>
                                                <th>Non-visit</th>
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
                                            $resultRetailer = DB::table('ims_tbl_visit_order')
                                                ->select('id','retailerid','foid','routeid','date','status','offer_type')                       
                                                ->where('date', date('Y-m-d'))
                                                ->where('foid', Auth::user()->id)
                                                ->where('retailerid', $retailers->retailer_id)
                                                ->where('offer_type', 'Regular')
                                                ->orderBy('entrydate','DESC')                    
                                                ->first();

                                            $checkRetailers = '';
                                            $checkStatus = '';
                                            if (sizeof($resultRetailer)>0) 
                                            {
                                                $checkRetailers = $resultRetailer->retailerid;
                                                $checkStatus    = $resultRetailer->status;
                                            }

                                            $resultBundle = DB::table('ims_tbl_visit_order')
                                                ->select('id','retailerid','foid','routeid','date','status','offer_type')                       
                                                ->where('date', date('Y-m-d'))
                                                ->where('foid', Auth::user()->id)
                                                ->where('retailerid', $retailers->retailer_id)
                                                ->where('offer_type', 'Bundle')
                                                ->orderBy('entrydate','DESC')                    
                                                ->first();
                                           
                                            $checkStatus2 = '';
                                            if (sizeof($resultBundle)>0) 
                                            {                                                
                                                $checkStatus2    = $resultBundle->status;
                                            }

                                            $resultExclusive = DB::table('ims_tbl_visit_order')
                                                ->select('id','retailerid','foid','routeid','date','status','offer_type')                       
                                                ->where('date', date('Y-m-d'))
                                                ->where('foid', Auth::user()->id)
                                                ->where('retailerid', $retailers->retailer_id)
                                                ->where('offer_type', 'Exclusive')
                                                ->orderBy('entrydate','DESC')                    
                                                ->first();
                                           
                                            $checkStatus3 = '';
                                            if (sizeof($resultExclusive)>0) 
                                            {                                                
                                                $checkStatus3    = $resultExclusive->status;
                                            }
                                            
                                            @endphp 
                                            <tr>
                                                <th>{{ $serial }}</th>
                                                <th>{{ $retailers->name }}</th>
                                                <th>{{ $retailers->owner }}</th>
                                                
                                                <th>
                                                   
                                                        <a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Regular') }}"> Regular</a>
                                                   
                                                </th>
                                                <th>
                                                     
                                                        <a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Bundle') }}"> Bundle</a>
                                                   

                                                </th>
                                                <th>
                                                     
                                                        <a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Exclusive') }}"> Exclusive</a>
                                                   
                                                </th>
                                                
                                               
                                               @if($checkRetailers!=$retailers->retailer_id)   
                                                
                                                <th><a href="{{ URL('/visit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Visit </a></th>
                                                <th><a href="{{ URL('/nonvisit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Non-visit </a></th>
                                                
                                                <th>
                                                
                                                @else                                                
                                                                                                
                                                <th></th>
                                                <th></th>
                                               
                                                
                                                <th>
                                                    @if($checkStatus==3)
                                                    Order
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
                                                <th colspan="6">No record found.</th>
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