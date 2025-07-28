@extends('eshop::masterPage')
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
                                           
                                            Challan NO : {{ $resultReqPro[0]->req_no }} <br /> 
											
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
									
							<table class="table table-bordered">
                                  <thead>									
										
										
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Delivery Qty</th>
                                            <th>Delivery Value</th>
                                        </tr>
										
                                    </thead>
                                    
                                    <tbody>
                                        
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalValue = 0;
                                        @endphp
                                        @foreach($resultReqPro as $items)   
                                        @php
                                        $totalQty += $items->delevered_qnty;   
                                        $totalValue += $items->delevered_value;                                   
                                        @endphp                                                        
                                        
                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->delevered_qnty }}</th>
												<th style="text-align: right;">{{ $items->delevered_value }}</th>
                                           
                                            </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
                                        </tr>
                                        
                                        @endforeach
                                        <tr>
                                            <th colspan="3" style="text-align: right;">Grand Total : </th>
                                            
                                            <th style="text-align: right;">{{ $totalQty }}</th>
                                            <th style="text-align: right;">{{ $totalValue }}</th>
                                        </tr>
                                       
                                       
                                    </tbody>
                                    <tfoot>
                                       

                                   
                                </table>
								
								<div class="row" style="text-align: center;">
									<div class="col-sm-12">
										<button type="button" class="btn bg-red waves-effect" onclick="printReport()">
											<i class="material-icons">print</i>
											<span>PRINT</span>
										</button>
									</div>
								</div>
                        
								<p></p>
								
                            
                               
                                <input type="hidden" name="req_id" id="req_id" value="">
                               
                        </div>
                       
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>

   

@endsection