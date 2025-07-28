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
                        <th>Return</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Return</th>
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
                    $resultRetailer = DB::table('tbl_visit_return_only')
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
                        @if($checkRetailers!=$retailers->retailer_id)
                        <th><a href="{{ URL('/fo/return-only-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Return Collect </a></th>
                        <th></th>
                        @else
                        <th>
                        @if($checkStatus!=3)
                            <a href="{{ URL('/fo/return-only-process/'.$retailers->retailer_id.'/'.$routeID) }}"> ADD </a>
                        @endif
                        </th>
                                             
                        <th>
                            @if($checkStatus==3)
                            Collected
                            @elseif($checkStatus==2)
                            Visit
                            @elseif($checkStatus==1)
                            Non-visit
                            @endif
                        </th>
                        @endif
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