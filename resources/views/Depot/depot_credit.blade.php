@extends('sales.masterPage') 
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                             DEPOT Payments List 
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
                        Depot Payments
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
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="depotPaymentList()">Search</button>
                        </div>
                    </div>

             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/depot_paymnet_process') }}" method="post" id="distriPay">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Payments</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                
                                <div class="col-sm-12">
                                   
                                  <label for="division">DEPOT:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="point_id" required="">
                                            <option value="">Please Select Depot</option>
                                            @foreach($depotList as $rowDepot)
                                            <option value="{{ $rowDepot->point_id }}">{{ $rowDepot->point_name }}</option>
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
                                            </select>
                                        </div>
                                    </div>
                                    
                            
                            <div id="bank_div" style="display:none">    
                                
                                    <label for="division">Select Bank:*</label>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
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
                                    
                                    <label for="division">Branch:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Branch Name" name="branch_name" />
                                        </div>
                                    </div>
                                    
                                    <label for="division">Cheque Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Cheque Date" id="fromdate" name="cheque_date" />
                                        </div>
                                    </div>
                                    
                            </div>

                                <div id="online_div" style="display: none">    
                                
                                    <label for="division">Select SSG Bank:*</label>
                                    
                                    <div class="form-group">
                                        <div class="form-line">
                                            <?php
                                                //echo $user_business_type;
                                                        
                                            $ssg_bank=DB::select("select * from tbl_master_bank where business_type=$user_business_type and status=0  order by id asc");
                                          
                                             ?>
                                            <select id="bank_name" name="ssgbank_name" class="form-control show-tick">          <?php foreach( $ssg_bank as $bank){?>
                                                    <option value="<?php echo $bank->id ?>"><?php  echo $bank->acctshortname."::".$bank->shortcode."::".$bank->bank_name;}?></option>
                                                   
                                    </select>
                                   
                                   </div>
                                   
                                 </div>
                                 
                                 
                               </div>
                               
                             
                                    
                                <div id="bank_child" style="display: none">
                                    
                                    <!--
                                    <label for="division">Branch:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Branch Name" name="branch_name" />
                                        </div>
                                    </div>
                                    
                                    <label for="division">Account No:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Accounts No" name="acc_no" />
                                        </div>
                                    </div>
                                
                                    
                                    <label for="division">Cheque No:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Cheque No" name="cheque_no" />
                                        </div>
                                    </div>
                                    
                                    
                                -->     
                                </div>
                                
                        <!-- Maung Added Field-->
                                   <div id="ref_div1" style="">
                                    <label for="division">REF.NO</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="REF.NO (CHEQUE/DD/PAY-ORDER,TT NO) " name="ref_no" />
                                        </div>
                                    </div>
                                </div>

                               
                                    <!-- Maung Added Field-->           

                                    <!--
                                    <label for="division">Accounts Pay:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="checkbox" class="form-control" placeholder="Accounts to pay" name="is_accounts_pay" value="YES" />
                                        </div>
                                    </div> -->
                                    
                                    
                                    <label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Amount" name="trans_amount" />
                                        </div>
                                    </div>
                                    <label for="division">Remarks/Note:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Remarks" id="remarks" name="payment_remarks" />
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
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Ack Status</th>
                    </tr>
                </thead>
                 <tbody>
             
             @if(sizeof($depotPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($depotPayment as $RowDepotPayment) 
                               
                        <th>{{$serial}}</th>
                        <th>{{$RowDepotPayment->point_name }}</th>
                        <th>{{$RowDepotPayment->trans_amount}}</th>
                        <th>{{$RowDepotPayment->payment_type}}</th>
                        <th>{{$RowDepotPayment->trans_date}}</th>
                        <th>{{$RowDepotPayment->ack_status}}</th>
                        
                        @if($RowDepotPayment->ack_status=="NO")
                        
                        <!--th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDepotPayment('{{ $RowDepotPayment->transaction_id }}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDepotPayment('{{ $RowDepotPayment->transaction_id}}')" style="width: 70px; margin-top: 0px;"></th-->
                     
                        @else
                            
                       {{--  <th></th> --}}
                        
                        @endif
                        
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="6">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Ack Status</th>

                       {{--  <th class="pull-right">Action</th> --}}
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
@endsection
