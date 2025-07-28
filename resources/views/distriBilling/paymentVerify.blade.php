@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PAYMENT VERIFY
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Verify
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
                    <h2>PAYMENT ACKNOWLEDGE LIST</h2>   
								
                </div>
                
                <div class="body">
				    
				<form action="{{ URL('/paymentVerify') }}" method="POST">	
				 {{ csrf_field() }}

                  @if(sizeof($paymentAckList) > 0)   
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
                                            <input  type="submit" name="ORDER_VERIFY" value="VERIFY" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
                                        </div>  
                                    </th>
                            <tr>

                                <th>SL</th>
								<th>Distributor</th>
								<th>Point</th>
                                <th>Division</th>
                                <th>Bank</th>
                                <th>Deposit Branch</th>
                                <th>Ref No</th>
                                <th>Method</th>
                                <th>Cheque Date</th>
                                <th>Deposit Amt</th>
                                <th>Verify</th> 
                                <th>Verify Remarks</th>
                                <th>Upadated By</th>
							    
                            </tr>
                        </thead>
                         @foreach($paymentAckList as $payment) 
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
                                    
                                    <input name="amtack[{{$payment->transaction_id}}]" type="radio" id="radio_yes[{{$dynLabel}}]" class="radio-col-red" value="CONFIRMED">
                                    <label for="radio_yes[{{$dynLabel}}]"> YES </label>
                                    
                                    <input name="amtack[{{$payment->transaction_id}}]" type="radio" id="radio_no[{{$dynLabel}}]" class="radio-col-red" value="NOT_CONFIRMED">
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
						
								
                         
                          @if(sizeof($paymentAckList) > 0)   
                         
                        </tbody>
                        <tfoot>
                              <tr>

                                <th>SL</th>
                                <th>Distributor</th>
                                <th>Point</th>
                                <th>Division</th>
                                <th>Bank</th>
                                <th>Deposit Branch</th>
                                <th>Ref No</th>
                                <th>Method</th>
                                <th>Cheque Date</th>
                                <th>Deposit Amt</th>
                                <th>Verify</th> 
                                <th>Verify Remarks</th>
                                <th>Upadated By</th>
                                
                            </tr>
                            <tr>
                                    <th colspan="13" >
                                        <div class="col-sm-12" align="right">
                                            <input  type="submit" name="ORDER_VERIFY" value="VERIFY" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
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