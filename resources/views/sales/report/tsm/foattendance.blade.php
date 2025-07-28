
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
            <div class="card">
                <div class="header">
                    <h2>FO Attendance</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">

                        <div class="col-sm-3">
                            <select id="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> FO </option> 
                                @foreach($territoryFO as $fos)
                                    <option value="{{ $fos->id }}">{{ $fos->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>                        

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="tmsFOAttendance()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($attendance) }} results 
                        </h5>
                    </div>                  
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>FO Name</th>
                                        <th>Date</th>
                                        <th>Route</th>
                                        <th>In Time</th>
                                        <th>In Location</th>
                                        <th>Out Time</th>
                                        <th>Out Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($attendance) > 0)   
                                        @php
                                        $serial =1;
                                        $totalValue = 0;
                                        @endphp

                                        @foreach($attendance as $attendances)

                                        @php
                                        if($attendances->retailerid=='')
                                        {
                                            $dist = DB::table('tbl_user_business_scope as us')
                                                    ->select('us.*','tbl_route.rname','tbl_route.point_id')
                                                    ->leftJoin('tbl_route','us.point_id','=','tbl_route.point_id')
                                                    ->where('us.user_id',$attendances->distributor)
                                                    ->first();

                                            $routeName= $dist->rname;
                                        }
                                        else
                                        {
                                            $dist = DB::table('tbl_retailer as us')
                                                    ->select('us.*','tbl_route.rname','tbl_route.point_id')
                                                    ->leftJoin('tbl_route','us.point_id','=','tbl_route.point_id')

                                                    ->where('us.retailer_id',$attendances->retailerid)
                                                    ->first();

                                            $routeName= $dist->rname;
                                        }

                                        @endphp
                                                          
                                        <tr>
                                            <th style="font-weight: normal;">{{ $serial }}</th>
                                            <th style="font-weight: normal;">{{ $attendances->display_name }}</th>
                                            <th style="font-weight: normal;">{{ $attendances->date }}</th>
                                            <th style="font-weight: normal;"> {{ $routeName }}</th>

                                            <th style="font-weight: normal;">{{ date('H:i', strtotime($attendances->entrydatetime)) }}</th>
                                            <th style="font-weight: normal;">{{ $attendances->location }}</th>                                            
                                            
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
                                            <th style="font-weight: normal;"> 
                                                @php
                                                    $outLo = DB::table('ims_attendence')
                                                    ->where('global_company_id', Auth::user()->global_company_id)
                                                    ->where('foid', $attendances->foid)
                                                    ->where('type', 3)
                                                    ->where('date', $attendances->date)
                                                    ->groupBy('date')
                                                    ->orderBy('date','DESC')
                                                    ->first();

                                                    //$outLo = '';
                                                    if(sizeof($outLo)>0)
                                                    {
                                                        echo $outLo->location;
                                                    }
                                                @endphp
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
    </div>
</section>

@endsection