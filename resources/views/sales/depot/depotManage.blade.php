@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            DEPOT MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Depot
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
                                    <h2>DIVISION</h2>
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
                                <select id="division" class="form-control show-tick" onchange="depotList()" data-live-search="true">
                                    <option value="">-- Select Division --</option>
                                    @foreach($division as $div)
                                    <option value="{{ $div->div_id }}"> {{ $div->div_name }} </option>
                                    @endforeach                         
                                </select>
                            </form>
                        </div>
                    </div>

                    <div id="showHiddenDiv">
                        
                        
                        <div class="card">
                            <div class="header">
                                <h2>
                                    Depot Data
                                </h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Depot Name</th>
                                                <th>Visit</th>
                                            </tr>
                                        </thead>
                                       <tbody>
                                            
                                            <tr>
                                                <th colspan="4">No record found.</th>
                                            </tr>
                                          
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                               <th>SL</th>
                                                <th>Depot Name</th>
                                                <th>Visit</th>
                                            </tr>
                                        </tfoot>
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