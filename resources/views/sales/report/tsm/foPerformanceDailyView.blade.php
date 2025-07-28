@extends('sales.masterPage')  
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        FO PERFORMANCE REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Order  
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
                    <h2>FO PERFORMANCE REPORT</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>
						<div class="col-sm-2">
                           <select id="channel" name="channel" class="form-control show-tick" data-live-search="true">
                               <option value="{{ $channelName->business_type_id }}" selected="">{{ $channelName->business_type }}</option>       
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select id="divistion" name="divisions" class="form-control show-tick" data-live-search="true" onchange="tsmDivisionWisePoints()" required="">
                                <option value="">Choose Division</option> 
                                @foreach($divisionName as $divisions)
                                    <option value="{{ $divisions->iDivId }}">{{ $divisions->div_name }}</option>
                                @endforeach                                                   
                            </select>
                        </div>

                        <div class="col-sm-2" id="foPoints">
                            <select name="pointsID" id="pointID" class="form-control show-tick" data-live-search="true"  required="">
                                <option value=""> Select Point</option>                
                            </select>
                        </div>
                       

                        <div class="col-sm-2" id="foDiv">
                            <select id="foID" name="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Select FO </option> 
                                                                                  
                            </select>
                        </div>              

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="foPerformanceDailyList()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>
            
            <div id="showHiddenDiv">

                <div class="card">
                    <div class="header">
                        <h5>
                            About results 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <tr>
                                    <th colspan="9">No record found.</th>
                                </tr>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection