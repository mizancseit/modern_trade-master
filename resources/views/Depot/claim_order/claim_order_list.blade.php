@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        CLAIM ORDER MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Claim Order Manage
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
                    <h2>CLAIM ORDER LIST</h2>   
								
                </div>
				
				
                
                <div class="body">
				
				<form action="{{ URL('/depot/DepotClaim') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromReqDate" id="fromdate1" class="form-control" value="" placeholder="from Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toReqDate" id="todate1" class="form-control" value="" placeholder="to Date">
                                </div>
                            </div>
                        </div>
						
						<!--
						<div class="col-sm-2">
                         			
											<select id="div_id" name="div_id" class="form-control show-tick">
													<option value="">Select Division</option>
												<?php foreach($divList as $rowDiv) { ?>
													<?php if(sizeof($resultClaimOrdList) && $resultClaimOrdList[0]->point_division == $rowDiv->div_id) { ?>
													 <option value="<?= $rowDiv->div_id ?>" selected><?= $rowDiv->div_name  ?></option>
													<?php  } else { ?>
													 <option value="<?= $rowDiv->div_id ?>"><?= $rowDiv->div_name  ?></option>
													<?php } ?>	
												<?php } ?>
											</select>
											
						</div>
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="business_type" name="business_type">
								<option value="" selected>LA</option>
								<option value="1" <?=(sizeof($resultClaimOrdList) && $resultClaimOrdList[0]->business_type_id == 1)?'selected':''; ?> >LIGHITING</option>
								<option value="2" <?=(sizeof($resultClaimOrdList) && $resultClaimOrdList[0]->business_type_id == 2)?'selected':''; ?>>ACCESSORIES</option>
							</select>
						</div>  -->
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="qnty_fillup" name="qnty_fillup">
								<option value="">Default</option>
								<option value="0" >Zero</option>
								<option value="fillup">Claim</option>
							</select>
						</div>
						
						<div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
						
					</div>

					
				
				</form>	
				
				
				    
				<form action="{{ URL('/depot/ClaimOrderProcess') }}" method="POST">	
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
                                <th>Deliv Qty</th>
                                <th>Rcvd Qty</th>
                                <th>Pending</th>
                                <th>Busket</th>
                                <th>Order</th>
							    
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultClaimOrdList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

							<?php $dynLabel = 1; ?>
							
                            @foreach($resultClaimOrdList as $RowClaim)
							
							<?php 
							
							$prodRecvd = 0;
							
							/*
							if($RowClaim->req_id == 153 and $RowClaim->prod_id == 406)
							{
								echo "Hellow"; exit;
							}
							*/
							
							$reTotClaimed = DB::select("SELECT SUM(rdet.req_qnty) as re_tot_claimed
												FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
												WHERE dr.claim_order_reference = '".$RowClaim->req_id."' 
												and rdet.product_id = '".$RowClaim->prod_id."'"); 
							
							/*							
							$reTotClaimed = DB::select("SELECT SUM(rdet.req_qnty) as re_tot_claimed
												FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
												WHERE dr.claim_order_reference = 153 
												and rdet.product_id = 406");
							*/					
							
							 $prodRecvd = $RowClaim->received_qnty;
							 if( sizeof($reTotClaimed)>0)
							 {
								$prodRecvd += $reTotClaimed[0]->re_tot_claimed; 
							 }
							 
							
							?>
							
							<?php 
							
							 if( $RowClaim->delevered_qnty > $prodRecvd) {  
							 
							 
							 ?>
							
                                           
                            <tr>
                                <th>{{ date('Y-m-d',strtotime($RowClaim->req_date)) }}</th>
                            	
								<th>
                                    {{ $RowClaim->req_no }}
                                </th>
								
								
								
                                <th>{{ $RowClaim->point_name }}</th>
                                <th>{{ $RowClaim->display_name }}</th>
                                <th>{{ $RowClaim->cat_name }}</th>
                                <th>{{ $RowClaim->pro_name }}</th>
                                <th>{{ $RowClaim->delevered_qnty }}</th>
                                <th>{{ $prodRecvd }}</th>
                                <th>{{ $RowClaim->delevered_qnty - $prodRecvd }}</th>
								<th>{{ 0 }}</th>
								
								<?php 
								
									if(isset($qnty_fillup) && $qnty_fillup == 'fillup' ) 
									    $text_fillUp =  ($RowClaim->delevered_qnty - $prodRecvd); 
								    else
										$text_fillUp =  0;  
								?>
								
								<th>
									<input type="text" size="3" name="claimord_qnty_<?=$RowClaim->req_id?>_<?=$RowClaim->cat_id?>_<?=$RowClaim->prod_id?>"  
									value="<?= $text_fillUp ?>"  />
								</th>
								
								<th>
									
									 <input type="hidden" name="req_id[]"   value="<?=$RowClaim->req_id?>">
									 <input type="hidden" name="req_no[]"   value="<?=$RowClaim->req_no?>">
									 <input type="hidden" name="point_id[]" value="<?=$RowClaim->point_id?>">
									 <input type="hidden" name="cat_id[]"   value="<?=$RowClaim->cat_id?>">
									 <input type="hidden" name="prod_id[]"  value="<?=$RowClaim->prod_id?>">
									 <input type="hidden" name="depo_price[]" value="<?=$RowClaim->depo_price?>">
									 <input type="hidden" name="sap_code[]" value="<?=$RowClaim->sap_code?>">
									
								</th>
                            
							</tr>
							
							<?php } // pending lock ?>
							
							
							
                            @php
                            $serial++;
							$dynLabel++;
						    @endphp
                            @endforeach

                        @else
                            <tr>
                                <th colspan="11">No record found.</th>
                            </tr>
                        @endif    
						
								<tr>
								    
									
									<th colspan="11" >
										<div class="col-sm-12" align="center">
											<input  type="submit" name="PENDING_ORDER" value="Claim To Billing" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
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