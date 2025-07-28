<div class="card">
    <div class="header">
        <h2>
            Outlet List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Outlet Name</th>
                        <th>Order</th>
                        <th>Visit</th>
                        <th>Non-visit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Outlet Name</th>
                        <th>Order</th>
                        <th>Visit</th>
                        <th>Non-visit</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultParty) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultParty as $retailers)


                         @php
                            $resultRetailerIMS = DB::table('eshop_visit_order') 
                                ->where('date', date('Y-m-d'))
                                ->where('officer_id', Auth::user()->id)
                                ->where('party_id', $retailers->party_id)
                                ->orderBy('id','DESC')                    
                                ->first(); 

                            $order = '';
                            $visit = '';
                            $non_visit    = '';
                            if (sizeof($resultRetailerIMS)>0) 
                            {
                                $order = $resultRetailerIMS->order;
                                $visit = $resultRetailerIMS->visit;
                                $non_visit    = $resultRetailerIMS->non_visit;
                            }
                            
                         @endphp 
                     
                    <tr>
                        <td>{{ $serial }}</td>
                        <td>{{ $retailers->address }}</td>
                     
                        <td><a href="{{ URL('/eshop-requisition-process/'.$retailers->party_id.'/'.$customerID) }}"> Order </a></td>
                        @if($order==1 || $visit==1 && $non_visit==0)
                        <td></td>
                        <td></td>
                        @else
                        <td><a href="{{ URL('/eshop-visit-order/'.$retailers->party_id.'/'.$customerID) }}"> Visit </a></td>
                        <td>
                            @if($order==0 && $visit==0 && $non_visit==1)
                            
                            @else
                            <a href="{{ URL('/eshop-nonvisit/'.$retailers->party_id.'/'.$customerID) }}"> Non-visit </a>
                            @endif
                        </td>
                        @endif

                        <td>
                             @if($order==1 && $visit==0 && $non_visit==0)
                            Order
                            @elseif($order==0 && $visit==1 && $non_visit==0)
                            Visit
                            @elseif($order==0 && $visit==0 && $non_visit==1)
                            Non-visit
                            @endif


                        </td>
                       
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">No record found.</td>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>