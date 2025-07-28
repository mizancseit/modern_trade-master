@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PENDING ORDER SUMMARY
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Pending Order Summary
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
                    <h2>PENDING ORDER SUMMARY</h2>   
								
                </div>
				
				
                
                <div class="body">
				
				<form action="{{ URL('/PendingOrderSummary') }}" method="get">
					
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
                                
							   <th colspan="3" style="text-align:center">PRODUCT INFO</th>
                               <th>STOCK</th>
                               <th colspan="2" style="text-align:center">MARKET</th>
                               <th>SAP</th>
                               <th colspan="2" style="text-align:center">PENDING</th>
                              
								<th>%</th>
                            </tr>
						
                            <tr>
                                
								<th>Group</th>
                                <th>Sap Code</th>
                                <th>Product</th>
								
                                <th>&nbsp;</th>
                                
								<th>Order</th>
                                <th>Value</th>
                                
								<th>&nbsp;</th>
                                
								<th>Order</th>
                                <th>Value</th>
								
								<th>&nbsp;</th>
                              
							    
                            </tr>
                        </thead> 
                        
                        <tbody>
                        @if(sizeof($resultPenOrdSummary) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;
							
							$tot_req_qnty = 0;
                            $tot_prod_Apprvd = 0;
                            $tot_pending_Apprvd = 0;

                            @endphp

							<?php $dynLabel = 1; ?>
							
                            @foreach($resultPenOrdSummary as $RowPending)
							
							<?php 
							
							$prodApprovd = 0;
							$reTotApprvd = DB::select("SELECT SUM(rdet.approved_qnty) as re_approved_q
												FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
												WHERE dr.re_order_reference = '".$RowPending->req_id."' 
												and rdet.product_id = '".$RowPending->prod_id."'");
							
							 $prodApprovd = $RowPending->approved_qnty;
							 if( sizeof($reTotApprvd)>0)
							 {
								$prodApprovd += $reTotApprvd[0]->re_approved_q; 
							 }
							 
							
							?>
							
							<?php 
							
							 if( $RowPending->req_qnty > $prodApprovd) {  
							 
							 
							 ?>
							
                                           
                            <tr>
                              
                            	
								 
                                <th>{{ $RowPending->cat_name }}</th>
                                <th>{{ $RowPending->sap_code }}</th>
                                <th>{{ $RowPending->pro_name }}</th>
                                <th>{{ $RowPending->stock_qty }}</th>
                                
								<th>{{ $RowPending->req_qnty }}</th>
								<th>{{ $RowPending->req_qnty * $RowPending->depo_price }}</th>
                                
								<th>{{ $prodApprovd }}</th>
                                
								<th>{{ $RowPending->req_qnty - $prodApprovd }}</th>
								<th>{{ ($RowPending->req_qnty - $prodApprovd) * $RowPending->depo_price }}</th>
								
								
								<th>{{ round((($RowPending->req_qnty - $prodApprovd)/ $RowPending->req_qnty)  * 100,2)  }}</th>
								
					        
							</tr>
							
							<?php 
							
							
							$tot_req_qnty += $RowPending->req_qnty;
                            $tot_prod_Apprvd += $prodApprovd;
                            $tot_pending_Apprvd += ($RowPending->req_qnty - $prodApprovd);
							
							
							} // pending lock ?>
							
							
							
                            @php
                            $serial++;
							$dynLabel++;
						    @endphp
                            @endforeach
							
							<tr>
                                <th colspan="3">Grand Total</th>
                                <th>{{ 0 }}</th>
                                <th>{{$tot_req_qnty}}</th>
								<th>{{ 0 }}</th>
							    <th>{{$tot_prod_Apprvd}}</th>
                                <th>{{$tot_pending_Apprvd}}</th>
								<th>{{ 0 }}</th>
								<th>{{ 0 }}</th>
                            </tr>
				
                        @else
                            <tr>
                                <th colspan="11">No record found.</th>
                            </tr>
                        @endif    
						
								
								
					<table class="table table-bordered table-striped table-hover">
                        <thead>
						
							<tr>
                               <th>&nbsp;</th>
                               <th>QTY </th>
                               <th>VALUE</th>
                            </tr>
							
							<tr>
                               <th>MARKET:</th>
                               <th>{{ $tot_req_qnty }} </th>
                               <th>{{ 0 }}</th>
                            </tr>
							
							<tr>
                               <th>SAP:</th>
                               <th>{{ $tot_prod_Apprvd }} </th>
                               <th>{{ 0 }}</th>
                            </tr>
							
							<tr>
                               <th>PENDING:</th>
                               <th>{{ $tot_pending_Apprvd }} </th>
                               <th>{{ 0 }}</th>
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