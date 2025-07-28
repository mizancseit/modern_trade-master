@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                       Payments Verify List 
                       <small> 
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Payments
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


        <!-- #END# Exportable Table -->

        <div class="card">
            <div class="header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Payments Verify List
                        </h2>

                    </div>

                </div>

            </div>

            <div class="body">

               <div class="row">
                <div class="col-sm-2">
                    <div class="input-group">
                        <div class="form-line">
                            <input type="text" name="fromdate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="input-group">
                        <div class="form-line">
                            <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <div class="form-line">
                          <select id="customer" name="customer" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Customer --</option>
                                @foreach($outletList as $customer)
                                <option value="{{$customer->customer_id}}"> {{ $customer->name }} - {{$customer->sap_code}} </option>
                                @endforeach                         
                            </select>
                    </div>
                  </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                      <div class="form-line">
                          <select class="form-control show-tick" id="payment_type" name="payment_type">
                              <option value="">Payment Type</option>
                              <option value="CASH">CASH</option>
                              <option value="CHEQUE">CHEQUE</option>
                              <option value="ON-LINE">ON-LINE</option>
                              <option value="PAY-ORDER">PAY-ORDER</option>
                              <option value="DD">DD</option>
                              <option value="TT">TT</option>
                          </select>
                      </div>
                  </div>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="accountsPaymentVerify2()">Search</button>
                </div>
            </div>
             <form action="{{ URL('/eshop-accounts-payments-verify-process') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

            <div class="row">
              <div class="col-sm-4" align="left">
                <h5>
                     About {{ sizeof($outletPayment) }} results 
                </h5>
              </div>
              <div class="col-sm-8" align="right">
                    <input  type="submit" name="payment_verify" value="VERIFY" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
                    <a href="{{URL('/eshop-accounts_payments_verify_download')}}" class="btn bg-red btn-block btn-sm waves-effect" style="width: 100px;">Download</a>  
                     
                </div>  
              </div>

            <div id="showHiddenDiv">
 
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Payment Date</th>
                                <th>Customer Name</th>
                                <th>SAP Code</th>
                                <th>Payment No</th>
                                <th>Bank Name</th>
                                <th>Bank A/C</th>
                                <th>Branch Name</th> 
                              <th>Reference No</th>
                                <th>Payment Amount</th>
                                <th>Adjust Amount</th>
                                <th>Actual Amount</th>
                                <th>Bank Charge</th>
                                <th>Payment Type</th>
                                <th>Verify</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                           @if(sizeof($outletPayment) > 0)   
                           @php
                           $serial =1;
                           @endphp

                           @foreach($outletPayment as $RowoutletPayment) 

                           <td>{{$serial}}</td>
                           <td>{{$RowoutletPayment->trans_date}}</td>
                           <td>{{$RowoutletPayment->name }}</td>
                           <td>{{$RowoutletPayment->sap_code }}</td>
                           <td>{{$RowoutletPayment->payment_no }}</td>
                           <td>{{$RowoutletPayment->bank_name }}</td>
                           <td>{{$RowoutletPayment->code }}</td>
                           <td>{{$RowoutletPayment->branch_name }}</td>
                           <td>{{$RowoutletPayment->ref_no }}</td>
                           <td>{{$RowoutletPayment->payment_amount}}</td>
                           <td>{{$RowoutletPayment->adjust_amount}}</td>
                           <td>{{$RowoutletPayment->net_amount}}</td>
                           <td>{{$RowoutletPayment->bank_charge}}</td>
                           <td>{{$RowoutletPayment->payment_type}}</td>
                           <td> 
                              <input type="checkbox" id="verify[{{$RowoutletPayment->transaction_id}}]" name="verify[{{$RowoutletPayment->transaction_id}}]" value="CONFIRMED">
                              <label for="verify[{{$RowoutletPayment->transaction_id}}]">Yes</label><br>
                            <input type="hidden" name="tran_id[]" value="{{$RowoutletPayment->transaction_id}}"> 
                          </td>
                        </tr>


                        @php
                        $serial++;
                        @endphp
                        @endforeach
                        @else
                        <tr>
                            <th colspan="15">No record found.</th>
                        </tr>
                        @endif     

                    </tbody>
                    <tfoot>
                     <tr>
                        <th>Sl</th>
                        <th>Payment Date</th>
                        <th>Customer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th> 
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Actual Amount</th>
                        <th>Bank Charge</th>
                        <th>Payment Type</th>
                         <th>Verify</th>
                        

                    </tr>
                </tfoot>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>
  </form>
</div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
<script type="text/javascript">
    function accountsPaymentVerify2()
        {   
          var fromdate    = document.getElementById('fromdate').value;
          var todate      = document.getElementById('todate').value;
          var customer      = document.getElementById('customer').value;
          var payment_type      = document.getElementById('payment_type').value;
          // alert(fromdate);
          var durl = '{{url("eshop-accounts_payments_verify_download")}}';
          var download_url = durl + '?todate='+todate+'&fromdate='+fromdate+'&customer='+customer+'&payment_type='+payment_type 
          $("#download_url").attr("href",download_url);
          $.ajax({
          method: "GET",
          url: '{{url('eshop-accounts-payments-verify-list')}}',
          data: {todate: todate,fromdate: fromdate,customer:customer,payment_type:payment_type}
            })
           .done(function (response)
           {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            }); 
        }
   </script>
   @endsection
