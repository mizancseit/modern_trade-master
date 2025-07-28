@extends('sales.masterPage')
@section('content')
    <section class="content">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="font-weight:">
                         <div class="body" id="printMe" >
                            
							<table width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%" height="49" style="text-align: center;"  valign="top">
												<font size="6">Super Star Company Ltd </font><br />
												UCEP Cheyne Tower (3rd Floor) <br />
												Phone : +88 02 8391751-6  <br />
												Fax : +88 02 8391723 <br />
												Email: info@ssgbd.com	<br />
												Website: www.ssgbd.com  <br />
												
												<font size="6">Cash Recipt </font><br />
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
                                
								<table width="100%" class="table" style="">
                                  
								  <thead>
                                    <tr>
                                        <td style="font-weight:bold;text-align: left;">Cash Recipt #: {{ $CollectionData[0]->reference_no  }} </td>
										<td style="font-weight:bold;text-align: right;">Date: {{ $CollectionData[0]->collection_date  }} </td>
                                    </tr>
                                  </thead>

									<tbody>
								       <tr>
									         <td style="font-size:18px;font-weight:bold;text-align: left;">Cash Received From &nbsp;<u> {{ $CollectionData[0]->name  }} </u> &nbsp;&nbsp;&nbsp; of &nbsp; TK 
											  <u> {{ $CollectionData[0]->collection_amount  }} </u> &nbsp; For  
											 
											 </td> 
											
											
									   </tr>
									   
									   <tr>
									   
									     <td> &nbsp; </td>
										 
											<td style="text-align:right;">  
										   
												<table width="100%" class="table" style="font-size:14px;font-weight:bold;">
                                  
													<thead>
														
														<tr>
															<td>Total Amount Due </td>
															<td> {{ $CollectionData[0]->retailer_opening_balance 
																		+ $CollectionData[0]->retailer_invoice_sales
															}} </td>
														</tr>
														
														<tr>
															<td>Collection </td>
															<td> {{ $CollectionData[0]->retailer_collection  }}  </td>
														</tr>
														
														<tr>
															<td>Balance Due </td>
															<td> {{ $CollectionData[0]->retailer_balance  }} </td>
														</tr>
														
													
														
													</thead>
													
												</table>	
										   
											</td>
									       
									   </tr>
									   
									    <tr>
											<td > <span style="text-align:left;font-size:16px;font-weight:bold;"> Payment Recived </span> 
											
											<span style="text-align:left;font-size:12px;font-weight:bold;">
											&nbsp;&nbsp;&nbsp;&nbsp;  Cash &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;
										
											Cheque &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Other 
											
											</span>
											
											</td>
															
										</tr>
									   
									  
									   
									   <tr>
									       <td style="font-size:16px;font-weight:bold;"> Cash Recipt </td>
										   <td> <div style="border: 1px solid; background-color:purple" > </div>
										   <div style="font-size:16px;font-weight:bold;margin-top:5px;margin-left:100px">Signed By</div> </td>
									   </tr>
									   
									</tbody>
								                                           
								</table>
						   
						   
						   
                        </div>

                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>
                        <p>&nbps;</p>

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection