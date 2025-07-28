@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Requisition Deatils
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Req Deatils List </a>
                            </small>
                        </h2>
                    </div>
 
                    </div>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">                        

                        @if(sizeof($resultReqPro)>0)
                        <div class="header">
                            <h2>ALL Requisition PRODUCT</h2>                            
                        </div>
                        <div class="body">
						
					<form action="{{ URL('/reqApproved') }}" method="POST">	
					
						{{ csrf_field() }}
				 
                            <table class="table table-bordered">
                                    <thead>
										
										<tr>
                                            <th colspan="4" style="padding-left:100px">Category: &nbsp; [ {{ $resultReqPro[0]->catname }} ]</th>                  
                                            <th colspan="4" style="padding-left:100px">Product: &nbsp; [ {{ $resultReqPro[0]->proname }} ]</th>                  
                                        </tr>
										
										<tr>
                                            <th colspan="8" style="padding-left:400px">SSG STOCK: &nbsp;{{ $resultReqPro[0]->stock_qty }}</th>                  
                                        </tr>
										
                                        <tr>
                                             <th>SL</th>
											 <th>Customer</th>
											 <th>DB Stock</th>
											 <th>Order Qty</th>
											 <th>Order Value</th>
											 <th>Confirm Qty</th>
											 <th>Confirm value</th>
                                        </tr>
										
                                    </thead>
                                    
                                    <tbody>
                                        
                                        

                                       
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalWastage = 0;
										
										$tot_db_stock = 0;
										$tot_order_qty = 0;
										$tot_order_value = 0;
										$tot_confirm_qty = 0;
										$tot_confirm_value = 0;
										
                                        @endphp
                                        @foreach($resultReqPro as $items)     


								<?php 

								$resultDBStock = DB::select("SELECT stock_qty FROM depot_stock WHERE point_id = '".$items->point_id."' 
																										and product_id = '".$items->prod_id."'");	

								  if(sizeof($resultDBStock)>0)
								  {
									  $db_stock_qty = $resultDBStock[0]->stock_qty;
								  } else {
									  $db_stock_qty = 0;
								  }

								?>										
                                        
										<tr>
                                            <th></th>
                                            <th colspan="9"></th>
                                        </tr>

                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->depot_in_charge }}</th>
												<th style="text-align: center;">{{ $db_stock_qty }}</th>
												<th style="text-align: center;">{{ $items->req_qnty }}</th>
												<th style="text-align: center;">{{ $items->req_value }}</th>
												
												<th style="text-align: center;">
												
												<input type="hidden" id="price{{$serial}}" name="price[]" value="{{$items->price}}">
												
													<input type="text" class="form-control" maxlength="4"  
													style="width: 80px;" id="qty{{$serial}}" name="approved_qnty_<?=$items->req_det_id?>" value="{{ $items->approved_qnty }}" 
													onkeyup="addAppQty({{$serial}})" />
												
												</th>
												
												
												<th style="text-align: right;">
													<input type="text" class="form-control" class="form-control" maxlength="4" 
													style="width: 80px;" id="value{{$serial}}" name="approved_value_<?=$items->req_det_id?>" value="{{ $items->approved_value }}" />
												</th>
												
												<input type="hidden" name="req_det_id[]" value="<?=$items->req_det_id?>">
                                           
										   
                                            </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
                                        </tr>
										
										<?php 
										$tot_db_stock += $db_stock_qty;
										$tot_order_qty += $items->req_qnty;
										$tot_order_value += $items->req_value;
										$tot_confirm_qty += $items->approved_qnty;
										$tot_confirm_value += $items->approved_value;
										?>
                                        
                                        @endforeach
										
									<tr  >
									 
												<th colspan="2" style="text-align: RIGHT;">GRAND TOTAL</th>  
												<th style="text-align: center;">{{ $tot_db_stock }}</th>
												<th style="text-align: center;">{{ $tot_order_qty }}</th>
												<th style="text-align: center;">{{ $tot_order_value }}</th>
												<th style="text-align: center;">{{ $tot_confirm_qty }}</th>
												<th style="text-align: center;">{{ $tot_confirm_value }}</th>
									</tr>
										
								<tr>
									<th colspan="8" >
									 
										<div class="col-sm-12" align="center">
											<input  type="submit" name="ORDER_APPROVED" value="UPDATE" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
										</div>	
									</th>
								</tr>	
                                        
                                       
                                       
                                    </tbody>
                                    <tfoot>
                                       

                                   
                                </table>
								
					</form>			
                                <p></p>
                               
                               
                               
                        </div>
                        @else
                        
                        <div class="header">
                            <h2>Product EMPTY</h2>                            
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </thead>
                            <tbody>                                        
                                <tr>
                                    <th colspan="7" style="color: #000; text-align: center;" align="center">
                                    <h4>YOUR Requisition PRODUCT IS EMPTY.</h4> <p></p><p></p>

                                    <div class="col-sm-4" style="margin-right: 40px;"></div>
                                   
                                    
                                     </th>                  
                                </tr>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </tbody>
                        </table>
                            
                        @endif
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>

   

@endsection