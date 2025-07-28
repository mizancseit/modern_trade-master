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
                                <select id="route" class="form-control show-tick" onchange="TuneAllRetailer(this.value)" data-live-search="true">
                                    <option value="">-- Please select route --</option>
                                    @foreach($routeResult as $routes)
                                    <option value="{{$routes->route_id}}"> {{ $routes->rname }} </option>
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
                                                <th>Order</th>
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
                                                <th>Order</th>
                                                <th>Visit</th>
                                                <th>Non-visit</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                      
                                            <tr>
                                                <th colspan="6">No record found.</th>
                                            </tr>
                                     
                                            
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