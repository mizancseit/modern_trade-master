<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-10">
                        <h2>PRODUCTS</h2>
                    </div>

                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                    </div>

                </div>                           
            </div>
            <div class="body">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Name</th>
                                <th>Wastage Qty</th>
                                <th>Requisition Qty</th>
                                <th>Value</th>
                               
                            </tr>
                        </thead>
                        
                        <tbody>
                            
                            @if(sizeof($resultProduct) > 0)
                                @php
                                $serial = 1;
								$date = '';
								$WastDate = '';
                                $distributor_id = Auth::user()->id;

                                if(sizeof($lastReq)>0){
                                //$date = date('Y-m-d H:i:s',strtotime($lastReq->req_date));
                                $date = $lastReq->req_date;
							    }
//dd($date);
                                @endphp

                                @foreach($resultProduct as $products)
                                @php
        if(sizeof($lastReq)>0){
            $wastageOrder  =  DB::select("SELECT e.product_id,sum(e.wastage_qty) AS qty,sum(e.wastage_value) AS wastageValue,sum(e.replace_delivered_qty) AS delQty FROM (
            SELECT tbl_order.order_date,tbl_order.update_date,0 as chalan_date,tbl_order.point_id,tbl_order.distributor_id,tbl_order_details.product_id,tbl_order_details.wastage_qty,tbl_order_details.wastage_value,tbl_order_details.replace_delivered_qty
            FROM tbl_order 
            INNER JOIN tbl_order_details ON tbl_order_details.order_id = tbl_order.order_id
            WHERE tbl_order.distributor_id =  $distributor_id AND tbl_order_details.product_id=$products->id AND tbl_order_details.replace_delivered_qty>0 AND date_format(tbl_order.update_date,'%Y-%m-%d %H:%i:%s')>'".$date."' AND tbl_order.order_type = 'Delivered' 
            UNION ALL
            SELECT tbl_wastage.order_date, 0 AS update_date,tbl_wastage.chalan_date,tbl_wastage.point_id,tbl_wastage.distributor_id,tbl_wastage_details.product_id,tbl_wastage_details.wastage_qty,tbl_wastage_details.p_total_price as wastage_value,tbl_wastage_details.replace_delivered_qty
            FROM tbl_wastage
            INNER JOIN tbl_wastage_details ON tbl_wastage_details.order_id = tbl_wastage.order_id
            WHERE tbl_wastage.distributor_id =  $distributor_id AND tbl_wastage_details.product_id=$products->id AND tbl_wastage_details.replace_delivered_qty>0 AND date_format(tbl_wastage.chalan_date,'%Y-%m-%d %H:%i:%s')>'".$date."'
			AND (tbl_wastage.order_type = 'Delivered' OR tbl_wastage.order_type = 'Declaration')
            ) AS e 
            group by e.product_id");
			
			//dd($products->id);
			
			/*
			echo "SELECT e.product_id,sum(e.wastage_qty) AS qty,sum(e.wastage_value) AS wastageValue,sum(e.replace_delivered_qty) AS delQty FROM (
            SELECT tbl_order.order_date,tbl_order.point_id,tbl_order.distributor_id,tbl_order_details.product_id,tbl_order_details.wastage_qty,tbl_order_details.wastage_value,tbl_order_details.replace_delivered_qty
            FROM tbl_order 
            INNER JOIN tbl_order_details ON tbl_order_details.order_id = tbl_order.order_id
            WHERE tbl_order.distributor_id =  $distributor_id AND tbl_order_details.product_id=$products->id AND tbl_order_details.replace_delivered_qty>0 AND date_format(tbl_order.order_date,'%Y-%m-%d %H:%i:%s')>'".$date."'
			AND tbl_order.order_type = 'Delivered'
            UNION ALL
            SELECT tbl_wastage.order_date,tbl_wastage.point_id,tbl_wastage.distributor_id,tbl_wastage_details.product_id,tbl_wastage_details.wastage_qty,tbl_wastage_details.p_total_price as wastage_value,tbl_wastage_details.replace_delivered_qty
            FROM tbl_wastage
            INNER JOIN tbl_wastage_details ON tbl_wastage_details.order_id = tbl_wastage.order_id
            WHERE tbl_wastage.distributor_id =  $distributor_id AND tbl_wastage_details.product_id=$products->id AND tbl_wastage_details.replace_delivered_qty>0 AND date_format(tbl_wastage.order_date,'%Y-%m-%d %H:%i:%s')>'".$date."'
			AND tbl_wastage.order_type = 'Delivered'
            ) AS e 
            group by e.product_id"; */
			//exit;
			
			
			
			/*
			echo '<pre/>';
			print_r($wastageOrder);
			*/
			
			
        }
        else{

            $wastageOrder  =  DB::select("SELECT e.product_id,sum(e.wastage_qty) AS qty,sum(e.wastage_value) AS wastageValue,sum(e.replace_delivered_qty) AS delQty FROM (
            SELECT tbl_order.order_date,tbl_order.point_id,tbl_order.distributor_id,tbl_order_details.product_id,tbl_order_details.wastage_qty,tbl_order_details.wastage_value,tbl_order_details.replace_delivered_qty
            FROM tbl_order 
            INNER JOIN tbl_order_details ON tbl_order_details.order_id = tbl_order.order_id
            WHERE tbl_order.distributor_id =  $distributor_id AND tbl_order_details.product_id=$products->id AND tbl_order_details.replace_delivered_qty>0
			AND tbl_order.order_type = 'Delivered'
            UNION ALL
            SELECT tbl_wastage.order_date,tbl_wastage.point_id,tbl_wastage.distributor_id,tbl_wastage_details.product_id,tbl_wastage_details.wastage_qty,tbl_wastage_details.p_total_price as wastage_value,tbl_wastage_details.replace_delivered_qty
            FROM tbl_wastage
            INNER JOIN tbl_wastage_details ON tbl_wastage_details.order_id = tbl_wastage.order_id
            WHERE tbl_wastage.distributor_id =  $distributor_id AND tbl_wastage_details.product_id=$products->id
			AND (tbl_wastage.order_type = 'Delivered' OR tbl_wastage.order_type = 'Declaration')
            ) AS e 
            group by e.product_id");
			//dd($wastageOrder);
         }
                                   $wasQty1 = '';
                                   $wasValue1 = 0;
                                   if(sizeof($wastageOrder)>0){
                                    $wasQty1 = $wastageOrder[0]->qty;
                                    $wasValue1 = $wastageOrder[0]->wastageValue;
                                   }                     
                                @endphp
                                <tr>
                                    <th>{{ $serial }}</th>
                                    <input type="hidden" id="price{{$serial}}" name="price[]" value="{{ $products->price }}">
                                    <input type="hidden" id="category_id{{$serial}}" name="category_id[]" value="{{ $products->category_id }}">
                                    <input type="hidden" id="unit{{$serial}}" name="unit[]" value="{{ $products->unit }}">
                                    <input type="hidden" id="produuct_id{{$serial}}" name="produuct_id[]" value="{{ $products->id }}">
                                    <th>{{ $products->name }}</th>
                                    <th><input type="number" class="form-control" id="wastageQty{{$serial}}" name="wastageQty[]" maxlength="3" pattern="[1-9]" min="1" value="{{ $wasQty1 }}" style="width: 80px;" onkeyup="totalsWastagesFO();" readonly="">
                                    </th> 
                                    <th><input type="number" class="form-control" id="qty{{$serial}}" name="qty[]" maxlength="3" pattern="[1-9]" min="1" value="{{$wasQty1}}" style="width: 80px;" onkeyup="wastageQty({{$serial}})" onmouseout="totalsFO();" readonly="">
                                    </th>
                                    <th><input type="number" class="form-control" id="value{{$serial}}" name="value[]" maxlength="10" value="{{ $wasValue1 }}" style="width: 80px;" readonly=""></th>
                                                                      
                                </tr>
                                @php
                                $serial ++;
                                @endphp

                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right; padding-top: 17px;" align="right">Total : </th>
                                <th><input type="text" class="form-control" id="totalWastage" name="totalWastage" readonly="" value="0" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalQty" name="totalQty" readonly="" value="0" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalValue" name="totalValue" readonly="" value="0" style="width: 80px;"></th>
                                
                               
                            </tr>
                        </tfoot>
                    </table>
                    <p></p>
                    <div class="row">
                        <div class="col-sm-10" style="text-align: right;">
                            &nbsp;
                        </div>

                        <div class="col-sm-2" style="text-align: right;">
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                        </div>
                    </div>                    
            </div>
        </div>
    </div>
</div>