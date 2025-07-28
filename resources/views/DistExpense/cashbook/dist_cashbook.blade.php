@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DISTRIBUTOR Expense List 
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
            Distributor Expense
        </h2>
    </div>

    <div class="body">
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Expense</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/dist_cashbook_process') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Expense</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">
                                
								<div class="col-sm-12">
                                   
								   <label for="division">DISTRIBUTOR:*</label>
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
                                	
									
									<label for="division">Expense Head:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                        <select class="form-control show-tick" name="perticular_head_id" required="">
                                        <option value="">Please Select Payment Type</option>
                                        
								<?php
									if(sizeof($ExPenseHead)>0)
									{										
										foreach($ExPenseHead as $rowExpenseHead)
										{  ?>
													
											<option value="<?php echo $rowExpenseHead->accounts_head_id ?>"><?php echo $rowExpenseHead->accounts_head_name ?></option>
											
								<?php	}
								
									}
					
									?>										
                                        
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
                                            <input type="text" class="form-control" placeholder="Transaction Date" name="trans_date" id="fromdate" required="" />
                                        </div>
                                    </div>
									
									<label for="division">Note:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Note" name="trans_description" />
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
                        <th>Distributor</th>
						<th>Accounts Head</th>
						<th>Trans Type</th>
						<th>Payment Amount</th>
                        <th>payment Date</th>
                        <th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotCashbook) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($depotCashbook as $RowDepotCashbook) 
                    <tr>
                        <th>{{$serial}}</th>
                        <th>{{$RowDepotCashbook->point_name }}</th>
                        <th>{{$RowDepotCashbook->accounts_head_name}}</th>
                        <th>{{$RowDepotCashbook->trans_type}}</th>
                        <th>{{$RowDepotCashbook->trans_amount}}</th>
                        <th>{{$RowDepotCashbook->trans_date}}</th>
                        <th><input type="button" name="cashbook_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDistributorCashbook('{{ $RowDepotCashbook->cash_book_id }}')" style="width: 70px;"">
                        <input type="button" name="cashbook_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDistributorCashbook('{{ $RowDepotCashbook->cash_book_id}}')" style="width: 70px; margin-top: 0px;"></th>
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
                        <th>Distributor</th>
						<th>Accounts Head</th>
						<th>Trans Type</th>
						<th>Payment Amount</th>
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
