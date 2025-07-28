@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            PARTY MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Order
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
            
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        
                        <div class="body">
                            <form id="form_validation" method="POST">
                               {{--  <select id="route" class="form-control show-tick" onchange="customerList(this.value)" data-live-search="true">
                                    <option value="">-- Please select Shop --</option>
                                    @foreach($routeResult as $routes)
                                    <option value="{{$routes->route_id}}"> {{ $routes->route_name }} </option>
                                    @endforeach                         
                                </select><br> --}}
                                <select id="customer" class="form-control show-tick" onchange="outletList(this.value)" data-live-search="true">
                                    <option value="">-- Please select Customer --</option>
                                    @foreach($customerResult as $customer)
                                    <option value="{{$customer->customer_id}}"> {{ $customer->name }} </option>
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
                                    Outlet List
                                </h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Outlet Name</th>
                                                <th>Order</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>SL</th> 
                                                <th>Outlet Name</th>
                                                <th>Order</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                      
                                            <tr>
                                                <th colspan="5">No record found.</th>
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