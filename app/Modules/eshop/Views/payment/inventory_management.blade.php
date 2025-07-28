@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2> 
                        Inventory Management  
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
                            Inventory Management
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
                <form action="{{ URL('/eshop_outlet_paymnet_process') }}" method="post" id="distriPay" enctype="multipart/form-data">
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
                                            <select class="form-control show-tick" name="party_id" onchange="outletListByCustomer(this.value)" >
                                                <option value=""> Select Customer</option>
                                                @foreach($outletList as $rowOutlet)
                                                <option value="{{ $rowOutlet->customer_id.'-'.$rowOutlet->sap_code }}">{{ $rowOutlet->name }} - {{$rowOutlet->sap_code}}</option>
                                                @endforeach
                                            </select> 
                                        </div>
                                    </div>
                                    <label for="division">Outlet:*</label>
                                      <div class="form-group">
                                        <div class="form-line" id="showHiddenDivoutletlist"> 
                                            <select class="form-control show-tick" name="party_id" >
                                                 
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
    </div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
<script type="text/javascript">
     
     
   </script>
   @endsection
