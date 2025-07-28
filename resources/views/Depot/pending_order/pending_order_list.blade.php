@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PENDING ORDER MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Pending Order Manage
                        </small>
                    </h2>
                </div>
                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>PENDING ORDER LIST</h2>   
								
                </div>
				
				
                
                <div class="body">
				
				<form action="{{ URL('/PendingOrderList') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromReqDate" id="fromdate1" class="form-control" value="{{ $sel_from_ReqDate }}" placeholder="from Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toReqDate" id="todate1" class="form-control" value="{{ $sel_to_ReqDate }}" placeholder="to Date">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-2">
                         			
											<select id="div_id" name="div_id" class="form-control show-tick">
													<option value="">Select Division</option>
												<?php foreach($divList as $rowDiv) { ?>
													<?php if($sel_div_id == $rowDiv->div_id) { ?>
													 <option value="<?= $rowDiv->div_id ?>" selected><?= $rowDiv->div_name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowDiv->div_id ?>"><?= $rowDiv->div_name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
						
						<div class="col-sm-3">
                         			
											<select id="cust_id" name="cust_id" class="form-control show-tick">
													<option value="">Customer</option>
												<?php foreach($custList as $rowCust) { ?>
													<?php if($sel_cust_id == $rowCust->id) { ?>
													 <option value="<?= $rowCust->id ?>" selected><?= $rowCust->display_name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowCust->id ?>"><?= $rowCust->display_name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
					
					</div>	
						
					<div class="row">	
						<div class="col-sm-3">
                         			
											<select id="cat_id" name="cat_id" class="form-control show-tick">
													<option value="">Category</option>
												<?php foreach($catList as $rowCat) { ?>
													<?php if($sel_cat_id == $rowCat->id) { ?>
													 <option value="<?= $rowCat->id ?>" selected><?= $rowCat->name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowCat->id ?>"><?= $rowCat->name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
						
						<div class="col-sm-3">
                         			
											<select id="prod_id" name="prod_id" class="form-control show-tick">
													<option value="">Product</option>
												<?php foreach($prodList as $rowProd) { ?>
													<?php if($sel_prod_id == $rowProd->id) { ?>
													 <option value="<?= $rowProd->id ?>" selected><?= $rowProd->name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowProd->id ?>"><?= $rowProd->name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="business_type" name="business_type">
								<option value="" selected>LA</option>
								<option value="1" <?=($sel_business_type == 1)?'selected':''; ?> >LIGHITING</option>
								<option value="2" <?=($sel_business_type == 2)?'selected':''; ?>>ACCESSORIES</option>
							</select>
						</div>
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="qnty_fillup" name="qnty_fillup">
								<option value="">Default</option>
								<option value="0" <?=($sel_qnty_fillup == 0)?'selected':''; ?>>Zero</option>
								<option value="fillup" <?=($sel_qnty_fillup == 'fillup')?'selected':''; ?>>Pending</option>
							</select>
						</div>
						
						<div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
						
					</div>

					
				
				</form>	
				
				
				    
				<form action="{{ URL('/PendingOrderProccess') }}" method="POST">	
				 {{ csrf_field() }}
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>Date</th>
								<th>Order</th>
								<th>Point</th>
                                <th>Customar</th>
                                <th>Group</th>
                                <th>Product</th>
                                <th>Aprvd QTY</th>
                                <th>Dlvrd</th>
                                <th>Pending</th>
                                <!-- <th>Busket</th> -->
                                <th>Order</th>
							    
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultPenOrdList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;
							
							$tot_req_qnty = 0;
                            $tot_prod_Delv = 0;
                            $tot_pending_Delv = 0;

                            @endphp

							<?php $dynLabel = 1; ?>
							
                            @foreach($resultPenOrdList as $RowPending)
							
							<?php 
							
							$prodDelv = 0;
							$reTotDel = DB::select("SELECT SUM(rdet.approved_qnty) as re_tot_delivered
												FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
												WHERE dr.re_order_reference = '".$RowPending->req_id."' 
												and rdet.product_id = '".$RowPending->prod_id."'");
							
							 $prodDelv = $RowPending->delevered_qnty;
							 if( sizeof($reTotDel)>0)
							 {
								$prodDelv += $reTotDel[0]->re_tot_delivered; 
							 }
							 
							
							?>
							
							<?php 
							
							 if( $RowPending->req_qnty > $prodDelv) {  
							 
							 
							 ?>
							
                                           
                            <tr>
                                <th>{{ date('Y-m-d',strtotime($RowPending->req_date)) }}</th>
                            	
								<th>
                                    {{ $RowPending->req_no }}
                                </th>
								
								
								
                                <th>{{ $RowPending->point_name }}</th>
                                <th>{{ $RowPending->display_name }}</th>
                                <th>{{ $RowPending->cat_name }}</th>
                                <th>{{ $RowPending->pro_name }}</th>
                                <th>{{ $RowPending->req_qnty }}</th>
                                <th>{{ $prodDelv }}</th>
                                <th>{{ $RowPending->req_qnty - $prodDelv }}</th>
								<!-- <th>{{ 0 }}</th>-->
								
								<?php 
								
									if(isset($qnty_fillup) && $qnty_fillup == 'fillup' ) 
									    $text_fillUp =  ($RowPending->req_qnty - $prodDelv); 
								    else
										$text_fillUp =  0;  
								?>
								
								<th>
									<input type="text" size="3" name="reord_qnty_<?=$RowPending->req_id?>_<?=$RowPending->cat_id?>_<?=$RowPending->prod_id?>"  
									value="<?= $text_fillUp ?>"  />
								</th>
								
								<th>
									
									 <input type="hidden" name="req_id[]" 	value="<?=$RowPending->req_id?>">
									 <input type="hidden" name="req_no[]" 	value="<?=$RowPending->req_no?>">
									 <input type="hidden" name="point_id[]" value="<?=$RowPending->point_id?>">
									 <input type="hidden" name="cat_id[]"   value="<?=$RowPending->cat_id?>">
									 <input type="hidden" name="prod_id[]" value="<?=$RowPending->prod_id?>">
									 <input type="hidden" name="depo_price[]" value="<?=$RowPending->depo_price?>">
									 <input type="hidden" name="sap_code[]" value="<?=$RowPending->sap_code?>">
									
								</th>
                            
							</tr>
							
							<?php 
							
							
							$tot_req_qnty += $RowPending->req_qnty;
                            $tot_prod_Delv += $prodDelv;
                            $tot_pending_Delv += ($RowPending->req_qnty - $prodDelv);
							
							
							} // pending lock ?>
							
							
							
                            @php
                            $serial++;
							$dynLabel++;
						    @endphp
                            @endforeach
							
							<tr>
                                <th colspan="6">Grand Total</th>
                                <th>{{$tot_req_qnty}}</th>
                                <th>{{$tot_prod_Delv}}</th>
                                <th>{{$tot_pending_Delv}}</th>
                                <th>0</th>
                                <th>0</th>
                            </tr>
				
                        @else
                            <tr>
                                <th colspan="11">No record found.</th>
                            </tr>
                        @endif    
						
								<tr>
								    
									
									<th colspan="11" >
										<div class="col-sm-12" align="center">
											<input  type="submit" name="PENDING_ORDER" value="ADD TO CART" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
										</div>	
									</th>
								</tr>	
                            
                        </tbody>
                    </table>
                    </div>   
				</form>	
              </div>

            </div>
        </div>
    </div>
</section>

@endsection