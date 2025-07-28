
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        FO Attendance
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
                            FO Attendance 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Distributor</th>
                                <th>Retailer</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($attendance) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($attendance as $attendances)
                                              
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $attendances->first_name }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $dname = DB::table('users')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('id', $attendances->distributor)
                                        ->first();
                                        
                                        if(sizeof($dname)>0)
                                        {
                                            echo $dname->display_name;
                                        }
                                    @endphp
                                </th>
                                <th style="font-weight: normal;">{{ $attendances->retailerName }}</th>
                                <th style="font-weight: normal;">{{ $attendances->location }}</th>
                                <th style="font-weight: normal;">{{ $attendances->date }}</th>
                                <th style="font-weight: normal;">{{ date('H:i', strtotime($attendances->entrydatetime)) }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $out = DB::table('ims_attendence')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('foid', $attendances->foid)
                                        ->where('type', 3)
                                        ->where('date', $attendances->date)
                                        ->groupBy('date')
                                        ->max('entrydatetime');

                                        $outTime = '';
                                        if(sizeof($out)>0)
                                        {
                                            $outTime = date('H:i', strtotime($out));
                                        }
                                    @endphp
                                        
                                    {{ $outTime }}
                                </th>
                            </tr>
                            @php
                            $serial++;
                            $totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="8" style="text-align: left;">Total Row : {{ $totalValue }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="8">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table> 
                        </div>
                    </div>
                </div>

                {{-- For Print --}}
                @if(sizeof($attendance) > 0)
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