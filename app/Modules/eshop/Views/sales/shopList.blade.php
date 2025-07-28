<div class="card">
    <div class="header">
        <h2>
            Visit Data
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Party Name</th>
                        <th>Address</th>
                        <th>Order</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Party Name</th>
                        <th>Address</th>
                        <th>Order</th>
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
                    $resultRetailerIMS = DB::table('ims_tbl_visit_order')
                        ->select('id','retailerid','foid','routeid','date','status')                       
                        ->where('date', date('Y-m-d'))
                        ->where('foid', Auth::user()->id)
                        ->where('retailerid', $retailers->retailer_id)
                        ->orderBy('id','DESC')                    
                        ->first();

                    $checkRetailers = '';
                    $checkStatus = '';
                    if (sizeof($resultRetailerIMS)>0) 
                    {
                        $checkRetailers = $resultRetailerIMS->retailerid;
                        $checkStatus    = $resultRetailerIMS->status;
                    }
                    
                    @endphp 
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $retailers->name }}</th>
                        <th>{{ $retailers->vAddress }}</th>
                        @if($checkRetailers!=$retailers->retailer_id)
                        <th><a href="{{ URL('/order-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Order </a></th>
                        <th><a href="{{ URL('/visit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Visit </a></th>
                        <th><a href="{{ URL('/nonvisit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Non-visit </a></th>
                        
                        <th></th>
                        @else
                        <th>
                        @if($checkStatus!=3)
                            <a href="{{ URL('/order-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Order </a>
                        @endif
                        </th>
                        <th></th>
                        <th></th>                        
                        <th>AA
                            @if($checkStatus==3)
                            Order
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
                        <th colspan="6">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>