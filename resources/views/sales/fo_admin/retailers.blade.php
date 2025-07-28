<div class="card">
    <div class="header">
        <h2>
            Retailer Data
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Owner</th>        
                        <th>Mobile</th>        
                        <th>Serial</th>
                        <th>Action</th>        
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Owner</th>
                        <th>Mobile</th>        
                        <th>Serial</th>
                        <th>Action</th> 
                    </tr>
                </tfoot>
                <tbody>
                    @if(sizeof($resultRetailer) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultRetailer as $retailers)

                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $retailers->name }}</th>
                        <th>{{ $retailers->owner }}</th>
                        <th>{{ $retailers->mobile }}</th>
                        <th>{{ $retailers->serial }}</th>
                        <th>
                            @php    
                            $requestSend = DB::table('tbl_retailer')
                                ->where('after_retailers', $retailers->retailer_id)
                                ->where('status', 1)
                                ->first();

                            if(sizeof($requestSend)>0)
                            {
                            @endphp

                            Request Pending

                            @php
                            }
                            else
                            {
                            @endphp
                            <a href="{{ URL('/admin/'.$retailers->serial.'/'.$retailers->retailer_id.'/'.$routeID) }}"> Add </a>
                            @php
                            }
                            @endphp

                        </th>
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