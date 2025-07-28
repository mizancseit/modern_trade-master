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