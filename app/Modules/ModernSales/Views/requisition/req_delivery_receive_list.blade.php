@extends('ModernSales::masterPage') 
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Delivery Challan
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Delivery List </a>
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
                                          
                                            Challan NO : {{ $resultReqPro[0]->delivery_chalan_no }} <br /> 
											
											Delivered By : {{ $resultReqPro[0]->delivered_by }} <br /> 
                                            Delivery Date : {{ date('d-m-Y',strtotime($resultReqPro[0]->delivered_date)) }} <br/>
                                            Mobile No :  {{ $resultReqPro[0]->delMobNo }}<br/>
                                           
											 <p> </p>	
											Collected By :  {{ $resultReqPro[0]->received_by}}<br/>
											
											<?php if($resultReqPro[0]->received_date) { ?>
                                            
											Collected Date : {{ date('d-m-Y',strtotime($resultReqPro[0]->received_date)) }} <br/>
											
											<?php } else { ?>
											
											Collected Date :  <br/>
											 
											<?php } ?>

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
				
				<form action="{{ URL('/reqReceive') }}" method="POST">		
								
								 {{ csrf_field() }}
								 
							<table class="table table-bordered">
                                  <thead>									
										
										
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Delivered Qty</th>
                                            <th>Delivered Value</th>
											<th>Received Qty</th>
                                            <th>Received Value</th>
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
                                        
										

                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->delevered_qnty }}</th>
												<th style="text-align: right;">{{ $items->delevered_value }}</th>
												
										<th style="text-align: right;">
												
									<input type="hidden" id="price{{$serial}}" name="price[]" value="{{$items->price}}">	
									
									<input type="hidden" id="received_qnty_{{$serial}}" name="received_qnty_{{$items->req_det_id}}" value="{{$items->delevered_qnty}}">	
									<input type="hidden" id="received_value_{{$serial}}" name="received_value_{{$items->req_det_id}}" value="{{$items->delevered_value}}">	
		
									<input type="hidden" id="req_det_id{{$serial}}" name="req_det_id[]" value="{{$items->req_det_id}}">			
												
												
												{{ $items->delevered_qnty }}
												
									</th>
												
									<th style="text-align: right;">
											
										{{ $items->delevered_value }}	
									
									</th>
                                           
                                            </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
                                        </tr>
                                        
                                        @endforeach
                                        
                                       
                                       
                                    </tbody>
                                    <tfoot>
                                       

                                   
                                </table>
								
								
								<div class="row" style="text-align: center;">
									
									<div class="col-sm-12">
											
											<div class="col-sm-4">
												<label for="division">GRN NO:*</label>
												<div class="form-group">
													<div class="form-line">
														<input type="text" class="form-control" placeholder="Goods Received No" name="grn_no"
														value="{{ 'GRN_' . $resultReqPro[0]->req_no }}" required="" />
													</div>
												</div>
											</div>		
										
											<div class="col-sm-4">	
												<label for="division">Received Date:*</label>
												<div class="form-group">
													<div class="form-line">
														<input type="text" class="form-control" id="fromdate" placeholder="Received Date" name="received_date"
														value="{{ date('d-m-Y') }}" required="" />
													</div>
												</div>
											</div>	
											
											
											<div class="col-sm-4">
												<label for="division">Received Note:</label>
												<div class="form-group">
													<div class="form-line">
														<input type="text" class="form-control"  placeholder="Received Note" name="received_note"
														value="" required="" />
													</div>
												</div>
											</div>	
									
									</div>
									
									<div class="col-sm-12">
										
										<input type="submit" name="RECEIVED" value="RECEIVED" class="btn bg-red btn-block btn-sm waves-effect" style="width: 100px;"">
										
										<!--
												<button type="button" class="btn bg-green waves-effect">
													<i class="material-icons">add_box</i>
													<span>    </span>
												</button>   -->
										
									</div>	
										
								 <input type="hidden" id="reqid" name="reqid" value="{{ $reqid }}">		
								 
								 <input type="hidden" id="partial_delivery_count" name="partial_delivery_count" value="{{ $resultReqPro[0]->partial_delivery_count }}">		
								 
									
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