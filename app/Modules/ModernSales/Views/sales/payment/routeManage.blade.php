@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            Opening Balance Management 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Opening Balance
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
                                <select id="route" class="form-control show-tick" onchange="openingOutletList(this.value)" data-live-search="true">
                                    <option value="">-- Please select Shop --</option>
                                    @foreach($routeResult as $routes)
                                    <option value="{{$routes->route_id}}"> {{ $routes->route_name }} </option>
                                    @endforeach                         
                                </select>
                            </form>
                        </div>
                    </div>

                    
                         <form action="{{ URL('/mts-add-opening-balance') }}" method="GET">
                            {{ csrf_field() }}  
                           
                            <div id="showHiddenDiv"> 
                            </div>
                        </form> 

                  
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection