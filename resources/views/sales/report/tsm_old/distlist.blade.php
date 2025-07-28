
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        DIstributor Details
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
                        </small>
                    </h2>
                </div>
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
           
           
            <div id="showHiddenDiv">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            Distributor Details 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>Distributor Id</th>
                                         <th>Name</th>
                                        <th>Email</th>
                                        <th>Cell Phone</th>                        
                                        <th>Designation</th>
                                                           
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($dist) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($dist as $list)                                       
                                    <tr>
                                    <th>{{ $list->sap }}</th> 
                                   
                                      <th>{{ $list->display_name }}</th> 
                                      <th>{{ $list->email }}</th> 
                                      <th>{{ $list->cell_phone }}</th> 
                                     <th>{{ $list->designation }}</th>
                                                                                      
                                      
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

                {{-- For Print --}}
                @if(sizeof($dist) > 0)
                <div class="card">
                    <div class="row" style="text-align: center; padding: 10px 10px; ">
                        <div class="col-sm-12">
                            <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                <i class="material-icons">print</i>
                                <span>PRINT...</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </div>
</section>

@endsection