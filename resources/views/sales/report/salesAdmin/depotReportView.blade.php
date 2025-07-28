@extends('sales.masterPage')
@section('content')
<section class="content"  id="contentReplace">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        DEPOT OPERATION REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Order  
                        </small>
                    </h2>
                </div>
                <div class="col-lg-3" style="text-align: right;">
                    <h2>                        
                        <small> 
                            <a href="JavaScript:void()" onclick="window.history.go(-1); return false;"> << BACK PREVIEW PAGE </a> | 
                            <a href="JavaScript:void()" onclick="activeMeu()" id="onlyMenu"> MENU SHOW </a>
                            <input type="hidden" id="onlyMenuValue" value="0">
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
                    <h2>DEPOT REPORT</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        {{-- <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>                                               

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allDepotReport()">Search</button>
                        </div>

                        <div class="col-sm-2">
                             <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none; padding-top: 8px;">
                        </div>
                    </div>                                  
                </div>
            </div>
            
            <div id="showHiddenDiv"> </div>

        </div>
    </div>
</section>

@endsection