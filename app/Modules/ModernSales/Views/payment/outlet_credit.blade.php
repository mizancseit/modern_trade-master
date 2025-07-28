@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                       Customer Payments List 
                       <small> 
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Point
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
                            Customer Payments
                        </h2>

                    </div>
                    <div class="col-lg-3">
                        <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Payments</button>
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
                <div class="col-sm-2">
                    <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="executivePaymentList()">Search</button>
                </div>
            </div>

            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/outlet_paymnet_process') }}" method="post" id="distriPay" enctype="multipart/form-data">
                    {{ csrf_field() }}    <!-- token -->
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #A62B7F">
                                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Payments</h4>
                            </div>
                            <div class="modal-body">
                             
                                <div class="row clearfix">
                                    
                                    <div class="col-sm-12">
                                     
                                      <label for="division">Customer:*</label>
                                      <div class="form-group">
                                        <div class="form-line">

                                            <select class="form-control show-tick" name="party_id" required="">
                                                <option value=""> Select Customer</option>
                                                @foreach($outletList as $rowOutlet)
                                                <option value="{{ $rowOutlet->customer_id.'-'.$rowOutlet->sap_code }}">{{ $rowOutlet->name }} - {{$rowOutlet->sap_code}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <label for="division">Payment Type:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <select class="form-control show-tick" id="payment_type" name="payment_type" onChange="distriBankDetails();" required="">
                                                <option value="">Please Select Payment Type</option>
                                                <option value="CASH">CASH</option>
                                                <option value="CHEQUE">CHEQUE</option>
                                                <option value="ON-LINE">ON-LINE</option>
                                                <option value="PAY-ORDER">PAY-ORDER</option>
                                                <option value="DD">DD</option>
                                                <option value="TT">TT</option>
                                                <option value="MR">MR</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div id="bank_div" style="display:none">    
                                        
                                        <label for="division">Select Bank  A/C:*</label>
                                        
                                        <div class="form-group">
                                            <div class="form-line">
                                                <select id="bank_name" name="bank_name" class="form-control show-tick">
                                                    <option value="">Select Bank</option>
                                                    @foreach($bankList as $bankList)
                                                    <option value="{{ $bankList->id.'-'.$bankList->bank_name.'-'.$bankList->code }}">{{ $bankList->bank_name }} - {{$bankList->code}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <label for="division">Branch: </label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Branch Name" name="branch_name" />
                                            </div>
                                        </div>
                                        
                                        <label for="division">Cheque Date:*</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                 <input type="text" name="cheque_date" id="todate1" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div id="online_div" style="display: none">    
                                        
                                        <label for="division">Select Bank A/C:*</label>
                                        
                                        <div class="form-group">
                                            <div class="form-line">
                                                
                                                <select id="bank_name" name="ssgbank_name" class="form-control show-tick">         
                                                    @foreach($ssgbankList as $ssgbankList)
                                                    <option value="{{ $ssgbankList->id.'-'.$ssgbankList->bank_name.'-'.$ssgbankList->code }}">{{ $ssgbankList->bank_name }} - {{$ssgbankList->code}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        
                                    </div>

                                    <label for="division">Payment From:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <select class="form-control show-tick" id="payment_from" name="payment_from" required=""> 
                                                <option value="E-Shop">E-Shop</option> 
                                                <option value="Moderntrade">Moderntrade</option> 
                                            </select>
                                        </div>
                                    </div>
                                     
                            <div id="ref_div1" style="">
                                <label for="division">REF.NO</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="REF.NO (CHEQUE/DD/PAY-ORDER,TT NO) " name="ref_no" />
                                    </div>
                                </div>
                            </div> 
                                    
                                    <label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Payment Amount" name="trans_amount" />
                                        </div>
                                    </div> 
                                   
                                    <label for="division">Remarks/Note: </label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Remarks" id="remarks" name="payment_remarks" />
                                        </div>
                                    </div>
                                    <label for="division">Attached file: </label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="file" id="user_photo" name="user_photo" />
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                            <button type="button" onclick="distrimodelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="showHiddenDiv">
          
            <div class="header">
                <h5>
                    About results 
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Customer Name</th>
                            <th>SAP Code</th>
                            <th>Payment No</th>
                            <th>Payment From</th>
                            <th>Bank Name</th>
                            <th>Bank A/C</th>
                            <th>Branch Name</th>
                            <th>Reference No</th>
                            <th>Payment Amount</th>
                            <th>Payment Type</th>
                            <th>Payment Date</th>
                            <th>Images</th>
                            <th>Remarks/Note</th>
                            <th>Ack Status</th> 
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
                       <td>{{$RowoutletPayment->name }}</td>
                       <td>{{$RowoutletPayment->sap_code }}</td>
                       <td>{{$RowoutletPayment->payment_no }}</td>
                       <td>{{$RowoutletPayment->payment_from }}</td>
                       <td>{{$RowoutletPayment->bank_name }}</td>
                       <td>{{$RowoutletPayment->code }}</td>
                       <td>{{$RowoutletPayment->branch_name }}</td>
                           <td>{{$RowoutletPayment->ref_no }}</td>
                       <td>{{$RowoutletPayment->payment_amount}}</td>
                       <td>{{$RowoutletPayment->payment_type}}</td>
                       <td>{{$RowoutletPayment->trans_date}}</td>
                       <td>@if($RowoutletPayment->upload_image!='')<img src="uploads/modernPayment/{{ $RowoutletPayment->upload_image }}" style="width: 60px; height: 40px;">@endif</td>
                       <td>{{$RowoutletPayment->payment_remarks}}</td>
                       <td>{{$RowoutletPayment->ack_status}}</td>
                       
                       @if($RowoutletPayment->ack_status=="NO")
                       
                        <td>
                            <!--input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editoutletPayment('{{ $RowoutletPayment->transaction_id }}')" style="width: 70px;"-->
                            
                            <a href=" {{ URL('/mts-outlet-payments-delete/'.$RowoutletPayment->transaction_id.'/'.'1') }}" onclick="return confirm('Are you sure you want to delete this item?')">
                                <button type="button" class="btn bg-red btn-block btn-lg waves-effect">Delete</button>
                            </a>
                        </td>
                              
                            @else
                            
                           <th></th>
                            
                            @endif
                            
                        </tr>
                        
                        
                        @php
                        $serial++;
                        @endphp
                        @endforeach
                        @else
                        <tr>
                            <td colspan="14">No record found.</td>
                        </tr>
                        @endif     
                        
                    </tbody>
                    <tfoot>
                     <tr>
                        <th>Sl</th>
                        <th>Customer</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        <th>Payment From</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                                <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Images</th>
                        <th>Ack Status</th>

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
            url: '{{url('outlet-payment-list')}}',
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
