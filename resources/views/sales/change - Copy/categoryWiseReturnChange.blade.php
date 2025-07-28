@extends('sales.masterPage')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 style="padding-top: 30px;">
                            NEW RETURN & CHNAGE
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/wastage') }}"> Return </a> / New Return & Change
                            </small>
                        </h2>
                    </div>
					<!--
                    <div class="col-lg-3">                        
                        <div class="info-box-2 bg-red">
                            <div class="icon">
                                <i class="material-icons">shopping_cart</i>
                            </div>
                            <a href="{{ URL('/return-change-bucket/'.$pointID.'/'.$routeid.'/'.$retailderid.'/'.$foID) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text">RETURN VALUE</div>
                                    <div class="number count-to" data-from="0" data-to="@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->total_return_value }} @else 0 @endif" data-speed="1000" data-fresh-interval="20">@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->total_return_value }} @else 0 @endif</div>
						        </div>
					        </a>
                        </div>
                    </div>
					
					<div class="col-lg-3">                        
                        <div class="info-box-2 bg-red">
                            <div class="icon">
                                <i class="material-icons">shopping_cart</i>
                            </div>
                            <a href="{{ URL('/return-change-bucket/'.$pointID.'/'.$routeid.'/'.$retailderid) }}" style="text-decoration: none;" title="Click To Bucket Details">
								<div class="content">
									<div class="text">CHANGE VALUE</div>
                                    <div class="number count-to" data-from="0" data-to="@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->total_change_value }} @else 0 @endif" data-speed="1000" data-fresh-interval="20">@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->total_change_value }} @else 0 @endif</div>
								</div>
								
                            </a>
                        </div>
                    </div>  -->
					
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif 

			@if(Session::has('failure'))
                <div class="alert alert-danger">
                {{ Session::get('failure') }}                        
                </div>
            @endif 			
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->retName }} @else STORE @endif </h2>                            
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/confirm-return-change') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="distributor_id" name="distributor_id" value="{{ $distributorID }}">
                <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                <input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $retailderid }}"> 
                <input type="hidden" id="fo_id" name="fo_id" value="{{ $foID }}"> 
                
				<input type="hidden" id="return_order_id" name="return_order_id" value="{{ $return_order_id }}">

                <div id="showHiddenDiv">                        
                
							{{-- Here Product List --}}
                
				   
						<!-- load product start -->
						
						    
						    
						<div class="row clearfix">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="card">
											<div class="header">                
												<div class="row">
													<div class="col-sm-10">
														<h2>PRODUCTS LIST</h2>
													</div>

													<div class="col-sm-2" style="text-align: right;">
														<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
													</div>

												</div>                           
											</div>
											<div class="body">
												<table class="table table-bordered table-striped table-hover dataTable js-exportable">
														<thead>
															<tr>
																<th>SL</th>
																<th>Return Product</th>
																<th>Qty</th>
																<th>Value</th>
																<th>Change Product</th>
																<th>Qty</th>		
																<th>Value</th>		
															
															</tr>
														</thead>
														{{-- <tfoot>
															<tr>
																<th colspan="2" style="text-align: right; padding-top: 17px;" align="right">Total : </th>
																<th><input type="text" class="form-control" id="totalWastageQty" name="totalWastageQty" value="0" readonly="" style="width: 80px;"></th>
															</tr>
														</tfoot> --}} 

														<tbody>
															@if(sizeof($resRetChangData) > 0)
																@php
																$serial = 1;
																@endphp

																@foreach($resRetChangData as $products)
																<tr>
																	<th>{{ $serial }}</th>
																	
																	
																<input type="hidden" id="return_cat_id" name="return_cat_id[]" value="{{ $products->return_cat_id }}">
																<input type="hidden" id="return_product_id" name="return_product_id[]" value="{{ $products->return_product_id }}">
																<input type="hidden" id="return_qty" name="return_qty[]" value="{{ $products->return_qty }}">
																<input type="hidden" id="return_value" name="return_value[]" value="{{ $products->return_value }}">
																<input type="hidden" id="change_cat_id" name="change_cat_id[]" value="{{ $products->return_cat_id }}">
                
																		
																	
																	
																	<th>{{ $products->retProdName }}</th>
																	
																	<th>{{ $products->return_qty }} </th>                                            
																	
																	<th>{{ $products->return_value }}</th>                                            
																{{-- <th> 
																	 
																		<select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
																				<option value="">Select Category</option>
																			@foreach($resultCategory as $cname)
																				@if($products->change_cat_id ==  $cname->id)
																					<option value="{{ $cname->id }}" selected>{{ $cname->name }}</option>
																				@else
																					<option value="{{ $cname->id }}">{{ $cname->name }}</option>
																				@endif
																			@endforeach
																		</select>   
																	
																	</th> --}}
																	
																
																	<th>
																	@php
																	$depo_price = 0;
																	$resultProduct = DB::table('tbl_product')
																		->select('id','name','depo')
																		->where('status', '0')
																		->where('category_id', $products->return_cat_id)
																		->get();
																	@endphp
																	
																		<div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
																			<select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
																				<option value="">Select Product</option>
																				<?php	foreach($resultProduct as $pname) {
																							if($products->change_product_id ==  $pname->id) { ?>
																							 <option value="{{ $pname->id }}" selected>{{ $pname->name }}</option>
																						
																					<?php 	
																						
																						if($pname->depo!=''):
																							$depo_price = $pname->depo;
																						else:
																						    $depo_price = 0;
																						endif;		
																						
																					}	else { ?>
																							<option value="{{ $pname->id }}">{{ $pname->name }}</option>
																				<?php	} 
																				} ?>
																			</select>
																			<input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="{{$depo_price}}">
																		</div>	
																		
																	</th>			
																	   
																<th>
																	<input type="text" class="form-control" id="changeQty{{$serial}}" name="change_qty[]" value="{{$products->change_qty}}" maxlength="3" style="width: 60px;" onkeyup="addChange({{$serial}})">
																</th>                                            
																	
																<th>
																	<input type="text" class="form-control" id="changeValue{{$serial}}" name="change_value[]" value="{{$products->change_value}}" maxlength="3" style="width: 60px;">
																</th>                                            
																
																
																</tr>
																@php
																$serial++;
																@endphp

																@endforeach
																
															@endif
														</tbody>
													</table>
													<p></p>
													<div class="row">
														<div class="col-sm-10" style="text-align: right;">
															&nbsp;
														</div>

														<div class="col-sm-2" style="text-align: right;">
															<button type="submit" class="btn bg-pink btn-block btn-lg submit waves-effect">Confirm</button>
														</div>
													</div>                    
											</div>
										</div>
									</div>
								</div>
						
						<!-- load product end-->
				
				
				
				
				</div>
            
			
			</form> 

        </div>
    </section>
@endsection