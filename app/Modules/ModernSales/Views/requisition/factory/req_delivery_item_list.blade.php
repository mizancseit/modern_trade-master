@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Item wise Delivery List
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Delivery Item List </a>
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
                    <div class="card" style="font-weight:">
                         <div class="body" id="printMe" >
						
                            <table width="100%">
							
                                    <thead>
										
										<tr>
                                            
											<th width="70%" height="49" align="left" valign="top">
                                         
											Point &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultReqPro[0]->point_name }}<br />
                                           
										    Req By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultReqPro[0]->depot_in_charge }} <br />

											Req Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d-m-Y', strtotime($resultReqPro[0]->req_date)) }} <br />
											
											<p> </p>
											Approved By &nbsp;&nbsp;&nbsp;: {{ $resultReqPro[0]->approved_by }} <br />
											
											Approved Date : {{ date('d-m-Y',strtotime($resultReqPro[0]->approved_date)) }} <br />
                                            
                                            {{-- <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo"> --}}
                                            
											</th>
                                            
											<th width="30%" align="left" valign="top">
                                          
                                            Challan NO : {{ 'CHN - ' . $resultReqPro[0]->req_no . '-' . ($resultReqPro[0]->partial_delivery_count + 1) }} <br /> 
											
											Billed By : {{ $resultReqPro[0]->billed_by }} <br /> 
                                            Billed Date : {{ date('d-m-Y',strtotime($resultReqPro[0]->billed_date)) }} <br/>
                                           
											 <p> </p>	
											Delivered By :  {{ 'FACTORY '}}<br/>
											
											Delivered Date : {{ date('d-m-Y') }} <br/>
											
                                            </th>
                                        
										</tr>
										
										  </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        
                                        
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                    </tbody>
                                </table>
				
				<form action="{{ URL('/reqDeliver') }}" method="POST">		
								
								 {{ csrf_field() }}
								 
							<table class="table table-bordered">
                                  <thead>									
										
										
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Billed Qty</th>
                                            <th>Billed Value</th>
											
											<th>Delvd Qty</th>
                                            <th>Delvd Value</th>
											
											<th>Today Delvd Qty</th>
                                            <th>Today Delvd Value</th>
                                        
										</tr>
										
                                    </thead>
                                    
                                    <tbody>
                                        
                                        

                                       
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalWastage = 0;
                                        @endphp
                                        @foreach($resultReqPro as $items)    

										<?php if($items->rem_delvd_qnty > 0 ) { ?>		
                                        
										

									<tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->billed_qnty }}</th>
												<th style="text-align: right;">{{ $items->billed_value }}</th>
												
												<th style="text-align: right;">
												
													<input type="hidden" id="price{{$serial}}" name="price[]" value="{{$items->price}}">	
													
													<input type="hidden" id="del_qnty{{$serial}}" name="del_qnty[]" value="{{$items->rem_delvd_qnty}}">										
													
													<input type="hidden" id="req_det_id{{$serial}}" name="req_det_id[]" value="{{$items->req_det_id}}">			
													
													<input type="hidden" id="pro_id{{$serial}}" name="pro_id_<?=$items->req_det_id?>" value="{{$items->pro_id}}">			
													<input type="hidden" id="cat_id{{$serial}}" name="cat_id_<?=$items->req_det_id?>" value="{{$items->cat_id}}">			
																
													{{ $items->delevered_qnty }}
												
												</th>
												
												
												<th style="text-align: right;">
												
												{{ $items->delevered_value }}
												
												</th>
												
												<th>
													<input type="text" class="form-control" maxlength="4"  
														style="width: 80px;" id="qty{{$serial}}" name="delevered_qnty_<?=$items->req_det_id?>" value="{{ $items->rem_delvd_qnty }}" 
													onkeyup="addRecQty({{$serial}})" />
												</th>
												
												<th style="text-align: right;">
												
													<input type="text" class="form-control" class="form-control" maxlength="4" 
														style="width: 80px;" id="value{{$serial}}" name="delevered_value_<?=$items->req_det_id?>" 
														value="{{  ($items->rem_delvd_value != ''?$items->rem_delvd_value:$items->billed_value) }}" readonly />
												
												</th>
												
												
												
                                           
									</tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
									<?php } ?>	 
                                        
                                        @endforeach
                                        
                                       	<input type="hidden" class="form-control"  name="partial_delivery_count" 
														value="{{ $items->partial_delivery_count }}" />
														
														
										<input type="hidden" class="form-control"  name="delivery_chalan_no" 
														value="{{  'CHN - ' . $resultReqPro[0]->req_no . '-' . ($resultReqPro[0]->partial_delivery_count + 1) }}" />				
                                       
									   
									  
									   
									   
                                    </tbody>
                                    <tfoot>
                                       

                                   
                                </table>
								
								
								<div class="row" style="text-align: center;">
									
									<div class="col-sm-12">
										
										<input type="submit" name="DELIVEREY" value="DELIVER" class="btn bg-red btn-block btn-sm waves-effect" style="width: 100px;"">
										
										<!--
												<button type="button" class="btn bg-green waves-effect">
													<i class="material-icons">add_box</i>
													<span>    </span>
												</button>   -->
										
									</div>	
										
								 <input type="hidden" id="reqid" name="reqid" value="{{ $reqid }}">		
									
								</div>
								
						</form> 		
                        
								<p></p>
								
                            
                               
                               
                        </div>
                       
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>

   

@endsection