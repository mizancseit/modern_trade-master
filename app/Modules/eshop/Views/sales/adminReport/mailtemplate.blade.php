 
            <html>
            <head> 
            <title>Product Approval</title>
            <style>
                body {
                    display: block;
                    font-size: 12px;
                }
            #customers {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
              }
              
              #customers td, #customers th {
                border: 1px solid #ddd;
                padding: 8px;
              }
              
              #customers tr:nth-child(even){background-color: #f2f2f2;}
              
              #customers tr:hover {background-color: #ddd;}
              
              #customers th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #f2f2f2 ; 
              }
              .warning{
                background-color: yellow;
              }
             
            
            </style> 
            </head>
            <body>
            <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row">
                        <div class="col-sm-8">
                        <span> 
                        PO No &nbsp;&nbsp;&nbsp;&nbsp;: {{$resultInvoice->po_no}} <br />
                        Customer Code :&nbsp; {{$customerInfo->sap_code}}<br />
                        Outlet Name  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{$resultInvoice->name}}
                        <br />
                        Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;  {{$customerInfo->address}}  <br /> 
                        Shiping Address&nbsp;:&nbsp;  {{$resultInvoice->address}}  
                        Invoice No :  {{$resultInvoice->order_no}}  <br />
                        Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :   
                        {{date('d M Y', strtotime($resultInvoice->order_date))}}
                        </span>

                         <p>Note: {{$remarks}}</p>
                        </div>
                        
                    </div>
                </div>
                <div class="link">
                Product Appoval link : <a href="{{$link}}">
                <span type="button" class="bg-pink" style=" 
                    background: #E91E63;
                    color: #fff;
                    border: 1px solid brown;
                    padding: 6px 7px 5px 10px;
                    float: right; " >Approved </button>
                </a> 
                </div>
                <div class="link">
                Product Appoval link : <a href="{{$link}}">
                <span type="button" class="bg-pink" style=" 
                    background: #E91E63;
                    color: #fff;
                    border: 1px solid brown;
                    padding: 6px 7px 5px 10px;
                    float: right; " >Approved </button>
                </a> 
                </div>

                <div class="body">
                    <div class="row"> 
                        <div class="body">
                        <table id="customers" style="width: 90%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Product Group</th>
                                    <th>Product Name</th>
                                    <th>SAP Code</th>
                                    <th>D.Price</th>
                                    <th>Unite Price</th>                                    
                                    <th>Order Qty</th>
                                    <th>Order Value</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($resultCartPro as $items)
                                @php
                                $resultComm = DB::table('eshop_categroy_wise_commission')
                                ->select('order_commission_value', 'commission')
                                ->where('eshop_categroy_wise_commission.order_id',$items->order_id)
                                ->where('eshop_categroy_wise_commission.cat_id', $items->catid) ->first();                                     
                                @endphp
                                <tr>
                                    <td></td>
                                    <td colspan="4">
                                        {{$items->catname}}  
                                        @if(sizeof($resultComm)>0) 
                                        <span style="padding-left: 250px; left: 0;">  Discount: {{$resultComm->commission }} 
                                        Value:  {{$resultComm->order_commission_value}} </span> 
                                        @endif
                                    </td>
                                </tr> 
                                    @php
                                    $itemsCount = 1;
                                    $reultPro  = DB::table('eshop_order_details')
                                    ->select('eshop_order_details.order_det_id','eshop_order_details.cat_id','eshop_order_details.order_id','eshop_order_details.order_qty','eshop_order_details.order_total_value','eshop_order_details.product_id','eshop_order_details.order_qty','eshop_order_details.p_unit_price','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.party_id','eshop_product.id','eshop_product.name AS proname' , 'eshop_product.distri', 'eshop_product.sap_code')
                                    //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                                    ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                                    ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                                    ->where('eshop_order.order_status','Confirmed')                        
                                    ->where('eshop_order.fo_id',$foMainId)                        
                                    ->where('eshop_order_details.order_id',$DeliveryMainId)
                                    ->where('eshop_product.category_id', $items->catid)    
                                    ->get(); 
                                    @endphp 
                                    @foreach ($reultPro as $itemsPro)
                                        @php 
                                            $subTotal += $itemsPro->order_total_value;
                                            $totalQty += $itemsPro->order_qty;
                                            $totalPrice += $itemsPro->order_total_value;

                                            if( $itemsPro->p_unit_price < $itemsPro->distri ){
                                                $eapproval_status =  1 ;
                                                $warning = 1;
                                            }else{
                                                $warning = 0;
                                            }
                                            @endphp 
                                           
                                            <tr>
                                                <td>{{$serial}}</td>
                                                <td></td>
                                                <td> {{$itemsPro->proname}} </td>
                                                <td> {{$itemsPro->sap_code}} </td>
                                                <td> {{$itemsPro->distri}} </td>
                                                <td <?php if($warning == 1){ ?> class="warning" <?php } ?>> {{$itemsPro->p_unit_price}} </td>
                                                
                                                <td style="text-align: center;"> {{number_format($itemsPro->order_qty,0)}}</td>
                                                <td style="text-align: center;">{{number_format($itemsPro->order_total_value,2)}}</td>
                                            </tr>
                                            @php
                                            $serial ++;
                                            @endphp       
                                    @endforeach
                                    @endforeach   
                                <tr>
                                    <th colspan="6" style="text-align: right;">Total</th>
                                    <th style="text-align: center;"> {{number_format($totalQty,0)}}  </th>
                                    <th style="text-align: center;"> {{number_format($totalPrice,2)}} </th>
                                </tr>
                                @php  
                                if(sizeof($orderCommission)>0){
                                @endphp
                                <tr>
                                    <th colspan="6" style="text-align: right;">Commission</th>
                                    <th>&nbsp;</th>
                                    <th style="text-align: center;">
                                     {{number_format($orderCommission->commission,2)}} 
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align: right;">Net Amount</th>
                                    <th>&nbsp;</th>
                                    <th style="text-align: center;">
                                    @php
                                    if(sizeof($orderCommission)>0){
                                    echo number_format($totalPrice-$orderCommission->commission ? $totalPrice-$orderCommission->commission : '0.00' ,2) ;
                                    }
                                    @endphp
                                    
                                    </th>
                                </tr>  
                                @php } @endphp   

                            </tbody> 
                        </table>
                        </div>  
                    </div>
                </div>
            </div> 
            </div>
            </body>
            </html> 