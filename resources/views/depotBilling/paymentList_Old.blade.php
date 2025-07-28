@extends('sales.masterPage') 
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PAYMENT ACKNOWLEDGEMENT MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Manage 
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
            <div class="card" style="overflow-y: auto;">
                
				<div class="header">
                    <h2>PAYMENT PENDING LIST</h2>   
								
                </div>
				
                <div class="body">
				
				<form action="{{ URL('/depotPaymentList') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromPaymentDate" id="fromdate1" class="form-control" value="" placeholder="Payment From Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toPaymentDate" id="todate1" class="form-control" value="" placeholder="Payment To Date">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-6">
                         			
											<select id="bank_name" name="bank_name" class="form-control show-tick">
													<option value="">Select Bank</option>
													<option value="AB Bank Limited">AB Bank Limited</option>
													<option value="Agrani Bank Limited">Agrani Bank Limited</option>
													<option value="Al-Arafah Islami Bank Limited">Al-Arafah Islami Bank Limited</option>
													<option value="Bangladesh Commerce Bank Limited">Bangladesh Commerce Bank Limited</option>
													<option value="Bangladesh Development Bank Limited">Bangladesh Development Bank Limited</option>
													<option value="Bangladesh Krishi Bank">Bangladesh Krishi Bank</option>
													<option value="Bank Al-Falah Limited">Bank Al-Falah Limited</option>
													<option value="Bank Asia Limited">Bank Asia Limited</option>
													<option value="BASIC Bank Limited">BASIC Bank Limited</option>
													<option value="BRAC Bank Limited">BRAC Bank Limited</option>
													<option value="Citibank N.A">Citibank N.A</option>
													<option value="Commercial Bank of Ceylon Limited">Commercial Bank of Ceylon Limited</option>
													<option value="Dhaka Bank Limited">Dhaka Bank Limited</option>
													<option value="Dutch-Bangla Bank Limited">Dutch-Bangla Bank Limited</option>
													<option value="Eastern Bank Limited">Eastern Bank Limited</option>
													<option value="EXIM Bank Limited">EXIM Bank Limited</option>
													<option value="First Security Islami Bank Limited">First Security Islami Bank Limited</option>
													<option value="Habib Bank Ltd.">Habib Bank Ltd.</option>
													<option value="ICB Islamic Bank Ltd.">ICB Islamic Bank Ltd.</option>
													<option value="IFIC Bank Limited">IFIC Bank Limited</option>
													<option value="Islami Bank Bangladesh Ltd">Islami Bank Bangladesh Ltd</option>
													<option value="Jamuna Bank Ltd">Jamuna Bank Ltd</option>
													<option value="Janata Bank Limited">Janata Bank Limited</option>
													<option value="Meghna Bank Limited">Meghna Bank Limited</option>
													<option value="Mercantile Bank Limited">Mercantile Bank Limited</option>
													<option value="Midland Bank Limited">Midland Bank Limited</option>
													<option value="Mutual Trust Bank Limited">Mutual Trust Bank Limited</option>
													<option value="National Bank Limited">National Bank Limited</option>
													<option value="National Bank of Pakistan">National Bank of Pakistan</option>
													<option value="National Credit & Commerce Bank Ltd">National Credit & Commerce Bank Ltd</option>
													<option value="NRB Commercial Bank Limited">NRB Commercial Bank Limited</option>
													<option value="One Bank Limited">One Bank Limited</option>
													<option value="Premier Bank Limited">Premier Bank Limited</option>
													<option value="Prime Bank Ltd">Prime Bank Ltd</option>
													<option value="Pubali Bank Limited">Pubali Bank Limited</option>
													<option value="Rajshahi Krishi Unnayan Bank">Rajshahi Krishi Unnayan Bank</option>
													<option value="Rupali Bank Limited">Rupali Bank Limited</option>
													<option value="Shahjalal Bank Limited">Shahjalal Bank Limited</option>
													<option value="Shimanto Bank Limited">Shimanto Bank Limited</option>
													<option value="Social Islami Bank Ltd.">Social Islami Bank Ltd.</option>
													<option value="Sonali Bank Limited">Sonali Bank Limited</option>
													<option value="South Bangla Agriculture & Commerce Bank Limited">South Bangla Agriculture & Commerce Bank Limited</option>
													<option value="Southeast Bank Limited">Southeast Bank Limited</option>
													<option value="Standard Bank Limited">Standard Bank Limited</option>
													<option value="Standard Chartered Bank">Standard Chartered Bank</option>
													<option value="State Bank of India">State Bank of India</option>
													<option value="The City Bank Ltd.">The City Bank Ltd.</option>
													<option value="The Hong Kong and Shanghai Banking Corporation. Ltd.">The Hong Kong and Shanghai Banking Corporation. Ltd.</option>
													<option value="Trust Bank Limited">Trust Bank Limited</option>
													<option value="Union Bank Limited">Union Bank Limited</option>
													<option value="United Commercial Bank Limited">United Commercial Bank Limited</option>
													<option value="Uttara Bank Limited">Uttara Bank Limited</option>
													<option value="Woori Bank">Woori Bank</option>
												</select>
											
						</div>
						
					</div>

					<div class="row">					
						
						<div class="col-sm-3">
                           <select class="form-control show-tick" id="payment_type" name="payment_type">
								<option value="">Payment Method</option>
								<option value="CASH">CASH</option>
								<option value="CHEQUE">CHEQUE</option>
								<option value="ON-LINE">ON-LINE</option>
								<option value="PAY-ORDER">PAY-ORDER</option>
								<option value="DD">DD</option>
								<option value="TT">TT</option>
							</select>
						</div>
						
						
						<div class="col-sm-2">
                           <select class="form-control show-tick" id="business_type" name="business_type">
								<option value="">LA</option>
								<option value="1">LIGHITING</option>
								<option value="2">ACCESSORIES</option>
							</select>
						</div>
						
						
						
						
						 <div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
                       
                    </div>  
				
				</form>	
				
				    
				<form action="{{ URL('/depotPaymentAcknowledge') }}" method="POST">	
				 {{ csrf_field() }}

                  @if(sizeof($paymentList) > 0)   
                    @php
					 $serial =1;
                     $dynLabel = 1; 
                     $sess_id=Auth::user()->id;
                     $user_id=DB::select("select * from users where id=$sess_id");
                    @endphp

                  
				
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
							
							<th colspan="13" >
								<div class="col-sm-12" align="right">
									<input  type="submit" name="ORDER_ACKNOWLEDGE" value="FORWARD TO ACCOUNTS" class="btn bg-green btn-block btn-sm waves-effect" style="width: 200px;">
								</div>  
							</th>
                            
							<tr>

                                <th>SL</th>
								<th>Depot</th>
								<th>Point</th>
                                <th>Division</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Ref No</th>
                                <th>Method</th>
                                <th>Cheque Date</th>
                                <th>Amount</th>
                                <th>ACK</th> 
                                <th>Remarks</th>
                                <th>Upadated By</th>
							    
                            </tr>
                        </thead>
                         @foreach($paymentList as $payment) 
                        <tbody>
                            @php
                            $dist_name=DB::select("select id,email,display_name from users where id=$payment->depot_in_charge");
                            
                            @endphp
                        <tr>
                        <th>{{$serial}}</th>
                        <th>{{$dist_name[0]->display_name}}</th>
                        <th>{{$payment->point_name}}</th>       
						<th>{{$payment->div_name}}</th>
                        <th>{{$payment->bank_name}}</th> 
                        <th>{{$payment->branch_name}}</th> 
                        <th>{{$payment->ref_no}}</th> 
                        <th>{{$payment->payment_type}}</th> 
                        <th>{{$payment->cheque_date}}</th>
                        <th>{{$payment->trans_amount}}</th>  
                           <th> <div class="demo-radio-button">
                                    
                                    <input name="amtack[{{$payment->transaction_id}}]" type="radio" id="radio_yes[{{$dynLabel}}]" class="radio-col-red" value="YES">
                                    <label for="radio_yes[{{$dynLabel}}]"> YES </label>
                                    
                                    <input name="amtack[{{$payment->transaction_id}}]" type="radio" id="radio_no[{{$dynLabel}}]" class="radio-col-red" value="NO">
                                    <label for="radio_no[{{$dynLabel}}]"> NO </label>
                                    
                                    
                                    <input type="hidden" name="reqid[]" value="{{$payment->transaction_id}}"  />
                                    
                                </div>  
                                </th>  
                        <th><textarea name="ack_remarks[{{$payment->transaction_id}}]"></textarea></th> 
                        <th>{{$user_id[0]->display_name}}</th>   
                        </tr>
						<input type="hidden" name="tran_id[{{$payment->transaction_id}}]" value="{{$payment->transaction_id}}">	
					 @php
                    $serial++;
                    $dynLabel++;
                    @endphp
                    @endforeach
                @else
							
                            <tr>
                                <th colspan="13">No record found.</th>
                            </tr>
                         @endif   
						
								
                         
                          @if(sizeof($paymentList) > 0)   
                         
                        </tbody>
                        <tfoot>
                              <tr>

                                <th>SL</th>
                                <th>Depot</th>
                                <th>Point</th>
                                <th>Division</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Ref No</th>
                                <th>Method</th>
                                <th>Cheque Date</th>
                                <th>Amount</th>
                                <th>ACK</th> 
                                <th>Remarks</th>
                                <th>Upadated By</th>
                                
                            </tr>
                            <tr>
                                    <th colspan="13" >
                                        <div class="col-sm-12" align="right">
                                            <input  type="submit" name="AMT_ACKNOWLEDGE" value="FORWARD TO ACCOUNTS" class="btn bg-green btn-block btn-sm waves-effect" style="width: 200px;">
                                        </div>  
                                    </th>

                                </tr>   
                                @endif
                                
                        </tfoot>
                    </table>    
				</form>	
              </div>

            </div>
        </div>
    </div>
</section>

@endsection