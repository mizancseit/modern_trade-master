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
                        <th>Retailer</th>
                        <th>Address</th>
                        <th colspan="3" style="text-align: center;">Order</th>
                        <th>Visit</th>
                        <th>Non-visit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Address</th>
                        <th colspan="3" style="text-align: center;">Order</th>
                        <th>Visit</th>
                        <th>Non-visit</th>
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
                    $resultRetailer = DB::table('ims_tbl_visit_order')
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
                        <th>{{ $retailers->owner }}</th>
                        <th><a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Regular') }}"> Regular </a></th>
                        <th><a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Bundle') }}"> Bundle </a></th>
                        <th><a href="{{ URL('/order-process-exception/'.$retailers->retailer_id.'/'.$routeID.'/'.'Exclusive') }}"> Exclusive </a></th>
                       
                       @if($checkRetailers!=$retailers->retailer_id)   
                        
                        <th><a href="{{ URL('/visit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Visit </a></th>
                        <th><a href="{{ URL('/nonvisit-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Non-visit </a></th>
                        
                        <th>
                        
                        
                        </th>
                        @else
                        
                        <th>
                        @if($checkStatus!=3)
                            <a href="{{ URL('/order-process/'.$retailers->retailer_id.'/'.$routeID) }}"> Order </a>
                        @endif
                        </th>
                        
                        <th></th>  
                        
                        <th>
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