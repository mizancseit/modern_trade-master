<div class="card" id="printMe">
    <div class="header">
        <h5>
            About {{ sizeof($resultVisitList) }} results 
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
                        <th>STATUS</th>                        
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultVisitList) > 0)   
                    @php
                    $serial =1;                    
                    @endphp

                    @foreach($resultVisitList as $visits)                                       
                    <tr>
                        <th>{{ $serial }}</th> 
                        <th>{{ $visits->first_name }}</th>
                        <th>{{ date('d M Y', strtotime($visits->date)) }}</th>                                                
                        <th>
                            @if($visits->status==3)
                            Order
                            @elseif($visits->status==2)
                            Visit
                            @elseif($visits->status==1)
                            Non-visit
                            @endif
                        </th>
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