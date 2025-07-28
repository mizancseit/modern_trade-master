@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            UTILITY MANAGEMENT 
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
            
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">
                        <div class="header">
                            <h2>NEW UTILITY</h2>                            
                        </div>

                        <form action="{{ URL('/utility-add') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                            <div class="body"> 
                                <div class="input-group">
                                    <select id="type" name="type" class="form-control show-tick" required="" onchange="utilityReason()">
                                        <option value="SUGGESTION">SUGGESTION</option>
                                        <option value="PROBLEM">PROBLEM</option>
                                        <option value="SERVICE">SERVICE</option> 
                                    </select>
                                </div>
                                <p></p>

                                <div class="input-group">
                                    <div id="reasonDiv">
                                        <select id="reason" name="reason" class="form-control show-tick" required="">
                                            <option value="0">-- No Reason --</option>                                        
                                        </select>
                                    </div>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <div class="col-md-12 align-left" style="padding-left:0px;">
                                        <div class="form-line">
                                            <input type="text" id="suggestion" name="suggestion" class="form-control" value="" placeholder="Suggestion" required="" maxlength="50">
                                        </div>
                                    </div>
                                </div>                               

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">                                        
                                        <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">
                <div class="header">
                <h2>
                UTILITY LIST 
                </h2>
                </div>

                <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Type</th>
                <th>Reason</th>
                <th>Suggestion</th>                        
                </tr>
                </thead>
                <tfoot>
                <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Type</th>
                <th>Reason</th>
                <th>Suggestion</th>                        
                </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultUtility) > 0)   
                @php
                $serial =1;
                @endphp

                @foreach($resultUtility as $utilitys)

                <tr>
                <th>{{ $serial }}</th>
                <th>{{ date('d M Y',strtotime($utilitys->date)) }}</th>                        
                <th>{{ $utilitys->type }}</th>
                <th>{{ $utilitys->reason }}</th>
                <th>{{ $utilitys->remarks }}</th>                        
                </tr>
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                <th colspan="5">No record found.</th>
                </tr>
                @endif    

                </tbody>
                </table>
                </div>
                </div>

                </div>
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection