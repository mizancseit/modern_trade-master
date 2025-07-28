<div class="card">
    <div class="header">
        <h2>
            Retailer List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Wastage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Wastage</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultRetailer) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultRetailer as $retailers)

                    @php
                    $resultRetailer = DB::table('tbl_visit_wastage')
                        ->select('id','retailerid','foid','routeid','date','status')                       
                        ->where('date', date('Y-m-d'))
                        ->where('foid', Auth::user()->id)
                        ->where('retailerid', $retailers->retailer_id)
                        ->orderBy('id','DESC')                    
                        ->first();

                    $checkRetailers = '';
                    $checkStatus = '';
                    if (sizeof($resultRetailer)>0) 
                    {
                        $checkRetailers = $resultRetailer->retailerid;
                        $checkStatus    = $resultRetailer->status;
                    }
                    
                    @endphp 
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $retailers->name }}</th>
                        <th><a href="{{ URL('/wastage-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Add  </a></th>
                        <th>
                            @if($checkStatus==3)
                            Collected
                            @elseif($checkStatus==2)
                            Visit
                            @elseif($checkStatus==1)
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