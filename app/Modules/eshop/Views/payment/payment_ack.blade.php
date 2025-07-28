@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                       Payments Ack List 
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
                            Payments Ack List
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
                    <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="accountsPaymentAck()">Search</button>
                </div>
            </div>

            
            <div id="showHiddenDiv">

                <div class="header">
                    <h5>
                        About {{ sizeof($outletPayment) }} results 
                    </h5>
                </div>
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
                                <th>Action</th>
                                
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
                           <td><a href="{{ URL('/eshop-accounts-payments-undo/'.$RowoutletPayment->transaction_id) }}">
                              <input type="button" class="btn bg-red btn-block btn-sm waves-effect" value="Undo" style="width: 70px;">
                            </a>
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
                         <th>Action</th>
                        

                    </tr>
                </tfoot>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>
</div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
<script type="text/javascript">
    function outletPaymentList()
    {  

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

            //alert('in');
            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
           // alert(fromdate);

           $.ajax({
            method: "GET",
            url: '{{url('accounts_payments_rece_report_list')}}',
            data: {todate: todate,fromdate: fromdate}
        })
           .done(function (response)
           {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });

       }
   </script>
   @endsection
