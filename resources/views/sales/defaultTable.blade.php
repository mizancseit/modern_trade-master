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
                        <a href="{{ URL('/new-order') }}">
                            <button type="button" class="btn bg-teal btn-block btn-lg waves-effect">New Order</button>
                        </a>
                    </div>
                </div>
                
            </div>

            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        <div class="header">
                            <h2>ROUTE</h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="route" class="form-control show-tick" onchange="allRetailer()">
                                    <option value="">-- Please select route --</option>
                                    @foreach($routeResult as $routes)
                                    <option value="{{ $routes->route_id }}"> {{ $routes->rname }} </option>
                                    @endforeach                         
                                </select>
                            </form>
                        </div>
                    </div>

                    <div id="showHiddenDiv">
                        
                        {{-- Here Retailer List --}}
                        
                    </div>

                    {{-- <div class="card">
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
                                            <th>Order</th>
                                            <th>Visit</th>
                                            <th>Non-visit</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                            <th>1</th>
                                            <th>Abdul Kader Store</th>
                                            <th><a href="{{ URL('/invoice') }}"> Order </a></th>
                                            <th>Visit</th>
                                            <th>Non-visit</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>2</th>
                                            <th>Alam Store</th>
                                            <th>Order</th>
                                            <th>Visit</th>
                                            <th>Non-visit</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>3</th>
                                            <th>Alamgir Store</th>
                                            <th>Order</th>
                                            <th>Visit</th>
                                            <th>Non-visit</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>4</th>
                                            <th>Ali Electric</th>
                                            <th>Order</th>
                                            <th>Visit</th>
                                            <th>Non-visit</th>
                                            <th></th>
                                        </tr>
                                        
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection