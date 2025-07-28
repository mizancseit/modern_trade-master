@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            PHYSICAL INVENTORY
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Physical Inventory
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
                    
                  
                    <div id="showHiddenDiv">
                        
                        {{-- Here Retailer List --}}
                        <div class="card">
                            <div class="header">
                                <h2>
                                    INVENTORY DATA
                                </h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                        <thead>
                                            <tr>
                                                <th>SL</th>                                                
                                                <th>Name</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(sizeof($depotResult) > 0)   
                                            @php
                                            $i =1;
                                            @endphp

                                            @foreach($depotResult as $depotList) 
                                            <tr>
                                                <td>{{$i++ }}</td>                                              
                                                <td>{{$depotList->point_name }}</td>
                                                <td>
                                                    <a href="{{ URL('/dist/stock-process/'.$depotList->point_id.'/'.'1') }}"> Stock In </a> | <a href="{{ URL('/dist/stock-process/'.$depotList->point_id.'/'.'2') }}"> Stock Out </a>
                                                </td>
                                            </tr>


                                            @endforeach
                                            @else
                                            <tr>
                                                <th colspan="4">No record found.</th>
                                            </tr>
                                            @endif     

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>SL</th>                                                
                                                <th>Name</th>
                                                <th>Option</th>
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