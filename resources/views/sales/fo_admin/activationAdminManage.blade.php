@extends('sales.masterPage')
@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            ACTIVATION/INACTIVE REQUEST MANAGEMENT 
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

            <div class="alert alert-danger" id="errorMsg" style="display: none;">
              <strong>Warning!</strong> Location Field Required
            </div>

            <div class="alert alert-success" id="successMsg" style="display: none;">
              <strong>Successfully!</strong> {{ Auth::user()->display_name }} <span id="successMsgShow"></span>
            </div>

            <!-- Exportable Table -->
           

            <div id="showHiddenDiv">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            Request List
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>FO Name</th>
                                        <th>Point</th>
                                        <th>Route</th>
                                        <th>Retailer</th>                        
                                        <th>Status</th>                        
                                        <th>Done</th>       
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($activation) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($activation as $activations)                                       
                                    <tr>
                                        <th>{{ $serial }}</th> 
                                        <th>{{ $activations->display_name }} <br /> {{ $activations->cell_phone }}</th>
                                        <th>{{ $activations->point_name }} </th>
                                        <th>{{ $activations->rname }}</th>
                                        <th>{{ $activations->name }}</th>                                               
                                        <th>
                                            @if($activations->status==0)
                                                Activation Request
                                            @else
                                                Inactive Request
                                            @endif
                                        </th> 
                                        <th>
                                            <a href="{{ URL('/admin/activation-done/'.$activations->id) }}">
                                            @if($activations->done==2)
                                                Pending
                                            @else
                                                Done
                                            @endif
                                            </a>
                                        </th> 
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
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection