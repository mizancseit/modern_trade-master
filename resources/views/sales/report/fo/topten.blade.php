
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        VISIT REPORT
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
                            FO Top 10 list 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>FO Id</th>
                                         <th>FO Name</th>
                                        <th>Total Value</th>
                                        <th>Total Qty</th>                        
                                        <th>Total Free Qty</th>
                                        <th>Total Free Value</th>                         
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($fotopten) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($fotopten as $top)                                       
                                    <tr>
                                    <th>{{ $top->fo_id }}</th> 
                                   <?php 
                                   $name=DB::select("select display_name from users where id=$top->fo_id"); ?>
                                  
                                    <th>@foreach($name as $display){{ $display->display_name}}@endforeach</th>
                                      <th>{{ $top->total }}</th> 
                                      <th>{{ $top->total_qty }}</th> 
                                      <th>{{ $top->total_free_qty }}</th> 
                                     <th>{{ $top->total_free_value }}</th>
                                                                                      
                                      
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
                @if(sizeof($fotopten) > 0)
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