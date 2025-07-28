
    <div class="card">
        <div class="header">
            <h5>
                About {{ sizeof($resultIms) }} results
            </h5>
        </div>                  
        <div class="body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Depot</th>
                            <th>Req No</th>
							<th>Point</th>
                            <th>Date & Category</th> 
                            <th style="text-align: right;">Requisition Qty</th>
                            <th style="text-align: right;">Requisition Value</th>
                        </tr>
                    </thead>
                    
                    <tbody>

                    @php
                    $s=1;
                    $totalMemo =0;
                    $totalQty =0;
                    $totalValue =0;
                    @endphp

                    @foreach($resultIms as $orders)
                   
                        <tr>
                            <th>{{ $s }}</th>
                            <th>{{ $orders->display_name }}</th>
                            <th>{{ $orders->req_no }}</th>
                            <th>{{ $orders->point_name }}</th>
                            <th colspan="2"> {{ date('d-m-Y',strtotime($orders->sent_date)) }}</th>
                        </tr>

                        @php
                        $categories      = DB::table('depot_req_details')
                        ->select(DB::raw("SUM(depot_req_details.req_qnty) as orderQty"),DB::raw("SUM(depot_req_details.req_value) as orderValue"),
						'depot_req_details.req_id','depot_req_details.cat_id','tbl_product_category.id','tbl_product_category.name')

                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'depot_req_details.cat_id')
                        ->where('depot_req_details.req_id',$orders->req_id)           
                        ->groupBy('depot_req_details.cat_id')           
                        ->get();

                        foreach ($categories as $key) {
                        
                         $totalQty +=$key->orderQty;
                         $totalValue +=$key->orderValue;
                        $s++;
                        @endphp
                            <tr>
                                <th colspan="4"></th>
                                <th style="text-align: right;">{{ $key->name }}</th>
                                <th style="text-align: right;">{{ $key->orderQty }}</th>
                                <th style="text-align: right;">{{ $key->orderValue }}</th>
                            </tr>
                        @php
                        }
                        @endphp
                    @php
                    $s++;
                    @endphp                                   
                    @endforeach
                    </tbody>
                    
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align: right;">Total</th>
                            <th style="text-align: right;">{{ $totalQty }}</th>
                            <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
