@extends('eshop::masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION Analysis MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Analysis 
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
                    <h2>REQUISITION ACKNOWLEDGE LIST</h2> 
								
                </div>
                
                <div class="body">
				
					<form action="{{ URL('/reqAllAnalysisList') }}" method="get">
					
					<div class="row">
					
						<div class="col-sm-3">
                         			
											<select id="div_id" name="div_id" class="form-control show-tick">
													<option value="">Division</option>
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
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
						
					</div>
				
				</form>	
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
						
					        <tr>
                                <th>SL</th>
							    <th>Group</th>
							    <th>SAP Code</th>
                                <th>Product Name</th>
                                <th>SSG Stock</th>
                                <th>Order Qty</th>
                                <th>Update Qty</th>
                                <th>Adj Qty</th>
                               
							    
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultAnalysisList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;
							
							$tot_ssg_stk = 0;
							$tot_req_qnty = 0;
							$tot_adjust_qty = 0;
							
                            @endphp

                            @foreach($resultAnalysisList as $ReqRow)
                                           
                            <tr>
                                
							  <?php if ( $ReqRow->adjust_qty > 0) { ?>	
								<th>{{ $serial }}</th>
                            	
                                <th> <font color="steelblue"> <a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank"> {{ $ReqRow->pro_cat_name }} </a> </font></th>
                                <th><font color="steelblue"> <a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->sap_code }} </a></font></th>
                                <th><font color="steelblue"><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->prod_name }} </a></font></th>
								<th><font color="steelblue"><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->ssg_stk }} </a></font></th>			
								
								<th><font color="steelblue"><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->req_qnty }} </a></font></th>

							<?php if($ReqRow->update_qnty != $ReqRow->req_qnty) { ?>
								<th><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank"><font color="red">{{ $ReqRow->update_qnty }}</font> </a></th>								
							<?php } else { ?>
								<th><font color="steelblue"><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->update_qnty }} </a></font></th>								
							<?php } ?>
							
								<th><font color="steelblue"><a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}" target="_blank">{{ $ReqRow->adjust_qty }} </a></font></th>		

							  <?php } elseif ( $ReqRow->adjust_qty < 0) { ?>
							 
								<th>{{ $serial }}</th>
                            	
                                <th> <font color=""> <a href="{{ URL('/reqOrderAnalysis/'.$ReqRow->prod_id) }}"> {{ $ReqRow->pro_cat_name }} </a> </font></th>
                                <th><font color="">{{ $ReqRow->sap_code }} </font></th>
                                <th><font color="">{{ $ReqRow->prod_name }} </font></th>
								<th><font color="">{{ $ReqRow->ssg_stk }} </font></th>			
								<th><font color="">{{ $ReqRow->req_qnty }} </font></th>			
								
							<?php if($ReqRow->update_qnty != $ReqRow->req_qnty) { ?>	
								<th><font color="red">{{ $ReqRow->update_qnty }} </font></th>			
							<?php } else { ?>	
								<th><font color="">{{ $ReqRow->update_qnty }} </font></th>	
							<?php } ?>	
								
								<th><font color="">{{ $ReqRow->adjust_qty }} </font></th>										
							
							 <?php } 
							 
							 
							  $tot_ssg_stk += $ReqRow->ssg_stk;
							 $tot_req_qnty += $ReqRow->req_qnty;
							 $tot_adjust_qty += $ReqRow->adjust_qty;
							 
							 ?>
							 
								
							 
                            </tr>
							
							
							
                            @php
                            $serial++;
                            @endphp
                            @endforeach
							
							
							<tr>
									<th colspan="4" style="padding-left:450px;">GRAND TOTAL</th>
									<th>{{ $tot_ssg_stk }}</th>
									<th>{{ $tot_req_qnty }}</th>
									<th>{{ $tot_adjust_qty }}</th>
								</tr>

                        @else
                            <tr>
                                <th colspan="8">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>

            </div>
        </div>
    </div>
</section>

@endsection