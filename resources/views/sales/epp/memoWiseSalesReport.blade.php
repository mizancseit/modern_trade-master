
@extends('sales.masterPage')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        Memo Wise Sales Report
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
            <div class="card">
                <div class="header">
                    <h2>Memo Wise Sales Report</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">

                        
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="memo" id="memo" class="form-control" value="" placeholder="Memo Number">
                                </div>
                            </div>
                        </div>                        

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="eppMemoWiseSalesReport()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                
            </div>
            
        </div>
    </div>
</section>

@endsection