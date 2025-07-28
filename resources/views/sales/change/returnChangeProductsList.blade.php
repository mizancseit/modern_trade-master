<div class="row clearfix">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="card">
											<div class="header">                
												<div class="row">
													<div class="col-sm-10">
														<h2>PRODUCTS LIST</h2>
													</div>

													<div class="col-sm-2" style="text-align: right;">
														<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
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
																
																<th>Change Category</th>
																<th>Product</th>
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
																	
																	
																	
																	<th>{{ $products->retProdName }}</th>
																	
																	<th>{{ $products->return_qty }} </th>                                            
																	
																	<th>{{ $products->return_value }}</th>                                            
																
																	<th> 
																	 
																		<select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})">
																				<option value="">Select Category</option>
																			@foreach($resultCategory as $cname)
																				@if($products->change_cat_id ==  $cname->id)
																					<option value="{{ $cname->id }}" selected>{{ $cname->name }}</option>
																				@else
																					<option value="{{ $cname->id }}">{{ $cname->name }}</option>
																				@endif
																			@endforeach
																		</select>   
																	
																	</th>
																
																	<th>
																	@php
																	
																	$resultProduct = DB::table('tbl_product')
																		->select('id','name','depo')
																		->where('status', '0')
																		->where('category_id', $products->change_cat_id)
																		->get();
																	@endphp
																	
																		<div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
																			<select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
																				<option value="">Select Product</option>
																					@foreach($resultProduct as $pname)
																						@if($products->change_product_id ==  $pname->id)
																							<option value="{{ $pname->id }}" selected>{{ $pname->name }}</option>
																						<?php $depo_price = $pname->depo; ?>
																						@else
																							<option value="{{ $pname->id }}">{{ $pname->name }}</option>
																						@endif
																					@endforeach
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
																$serial ++;
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
															<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
														</div>
													</div>                    
											</div>
										</div>
									</div>
								</div>