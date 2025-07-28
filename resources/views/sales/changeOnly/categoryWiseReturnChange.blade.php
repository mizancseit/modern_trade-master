@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 style="padding-top: 30px;">
                            NEW RETURN
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/return-only-order') }}"> Return </a> / New Return
                            </small>
                        </h2>
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
                        <div class="header"> 
                            <h2>@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->retName }} @else STORE @endif </h2>                            
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/confirm-only-return-change') }}" method="POST">
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
																
																
																<th>Approved Product</th>
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
																	
																	
																<input type="hidden" id="return_cat_id" name="return_cat_id[]" value="{{ $products->return_only_cat_id }}">
																<input type="hidden" id="return_product_id" name="return_product_id[]" value="{{ $products->return_only_product_id }}">
																<input type="hidden" id="return_qty" name="return_qty[]" value="{{ $products->return_only_qty }}">
																<input type="hidden" id="return_value" name="return_value[]" value="{{ $products->return_only_value }}">
                
																		
																	
																	
																	<th>{{ $products->retProdName }}</th>
																	
																	<th>{{ $products->return_only_qty }} </th>                                            
																	
																	<th>{{ $products->return_only_value }}</th>       

																	
																	
																
																	<th>
																		
																			
																			{{$products->retProdName}}
																			
																			<input type="hidden" id="approved_only_cat_id" name="approved_only_cat_id[]" value="{{ $products->return_only_cat_id }}">																		
																			<input type="hidden" id="approved_only_product_id" name="approved_only_product_id[]" value="{{ $products->return_only_product_id }}">																		
																
																			
																		
																		
																	</th>			
																	   
																<th>
																	<input type="text" class="form-control" id="approved_only_qty{{$serial}}" name="approved_only_qty[]" value="{{$products->return_only_qty}}" maxlength="3" style="width: 60px;" readonly="readonly" onkeyup="addChange({{$serial}})">
																</th>                                            
																	
																<th>
																	<input type="text" class="form-control" id="approved_only_value{{$serial}}" name="approved_only_value[]" value="{{$products->return_only_value}}" maxlength="3" style="width: 60px;">
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
															<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
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