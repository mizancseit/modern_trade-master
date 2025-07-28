
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        ATTENDANCE REPORT
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
                    <h2>ATTENDANCE REPORT </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">

                        <div class="col-sm-3"></div>
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="attendanceReports()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card" id="printMe">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultAttendanceList) }} results 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>FO</th>
                                        <th>DATE</th>                        
                                        <th>IN TIME</th>                        
                                        <th>OUT TIME</th>                        
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultAttendanceList) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($resultAttendanceList as $visits)                                       
                                    <tr>
                                        <th>{{ $serial }}</th> 
                                        <th>{{ $visits->first_name }}</th>
                                        <th>{{ date('d M Y', strtotime($visits->date)) }}</th>                                               
                                        <th>{{ date('H:i', strtotime($visits->entrydatetime)) }}</th>
                                        <th> 
                                        @php
                                        $out = DB::table('ims_attendence')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('foid', Auth::user()->id)
                                        ->where('type', 3)
                                        ->where('date', $visits->date)
                                        ->groupBy('date')
                                        ->max('entrydatetime');

                                        $outTime = '';
                                        if(sizeof($out)>0)
                                        {
                                            $outTime = date('H:i', strtotime($out));
                                        }
                                        @endphp
                                        
                                        {{ $outTime }}</th>
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

                {{-- For Print --}}
                @if(sizeof($resultAttendanceList) > 0)
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