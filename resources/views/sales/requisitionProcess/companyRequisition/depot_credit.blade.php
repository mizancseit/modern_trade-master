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
        <h2>
            Depot Payments
        </h2>
    </div>

    <div class="body">
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Payments</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/depot_paymnet_process') }}" method="post">
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
                                        <select class="form-control show-tick" name="payment_type" required="">
                                        <option value="">Please Select Payment Type</option>
                                        
											<option value="ON-LINE">ON-LINE</option>
											<option value="CASH">CASH</option>
											<option value="CHEQUE">CHEQUE</option>
											<option value="PAY-ORDER">PAY-ORDER</option>
											<option value="DD">DD</option>
											<option value="TT">TT</option>
                                        
                                        </select>

                                        </div>
                                    </div>
									
									
									<label for="division">Payment Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Amount" name="trans_amount" required="" />
                                        </div>
                                    </div>
									
									<label for="division">Payment Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Transaction Date" name="trans_date" required="" />
                                        </div>
                                    </div>
									
									
                                      
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                            <button type="button" onclick="modelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <br>
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>payment Date</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($depotPayment as $RowDepotPayment) 
                    <tr>
                        <th>{{$serial}}</th>
                        <th>{{$RowDepotPayment->point_name }}</th>
                        <th>{{$RowDepotPayment->trans_amount}}</th>
                        <th>{{$RowDepotPayment->payment_type}}</th>
                        <th>{{$RowDepotPayment->trans_date}}</th>
                        <th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDepotPayment('{{ $RowDepotPayment->transaction_id }}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDepotPayment('{{ $RowDepotPayment->transaction_id}}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>payment Date</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
