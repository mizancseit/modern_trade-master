@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DEPOT Collection List 
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
            Depot Collection
        </h2>
    </div>

    <div class="body">
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">New Collection</button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/DepotCollectionProcess') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">New Collection</h4>
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
									
									<label for="division">Retailer:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <!-- <select class="form-control show-tick" name="retailer_id" required="" onchange="getRetailerInvoice(this.value)"> -->
                                        
										<select class="form-control show-tick" name="retailer_id" required="">
											<option value="">Please Select Retailer</option>
											@foreach($retailerList as $rowRetailer)
											<option value="{{ $rowRetailer->retailer_id }}">{{ $rowRetailer->name }}</option>
											 @endforeach
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Collect By:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="fo_id" required="">
											<option value="">Please Select FO</option>
											@foreach($foList as $rowFo)
											<option value="{{ $rowFo->id }}">{{ $rowFo->display_name }}</option>
											 @endforeach
                                        </select>

                                        </div>
                                    </div>
                                	
									<!--
									<label for="division">Invoice No:</label>
									<div class="form-group">
										<div class="form-line" id="invoice_no">
                                            <select class="form-control show-tick" name="invoice_no">
                                                <option value="">Select Invoice</option>

                                            </select>
                                        </div>
                                    </div>
									
									
									<div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Invoice No" name="invoice_no" />
                                        </div>
                                    </div> -->
									
									<label for="division">Money Recipt:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Money Recipt" name="reference_no" required="" />
                                        </div>
                                    </div> 
									
									
									
									<label for="division">Collection Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Collection Amount" name="collection_amount" required="" />
                                        </div>
                                    </div>
									
									<label for="division">Collection Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="fromdate" placeholder="Collection Date" name="collection_date" required="" />
                                        </div>
                                    </div>
									
									
									<label for="division">Collection Note:</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Collection Note" name="collection_note" required="" />
                                        </div>
                                    </div>
									
									<!--
									<label for="division">Commission Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                      
									  <select class="form-control show-tick" name="commission_type" >
                                        <option value="">Please Select Commission Type</option>
                                        
											<option value="REGULAR">REGULAR</option>
											<option value="SPECIAL">SPECIAL</option>
							            
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Commission Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Commission Amount" name="commission_amount" />
                                        </div>
                                    </div> -->
									
									
                                      
                                    
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
						<th>Depot Name</th>
                        <th>Party/Shop Name</th>
                        <th>Address</th>
                        <th>Collection Amount</th>
                        <th>Collection Date</th>
                        <th>Collected By</th>
                        <th>Print</th>
                        
					<!--	
						<th>Commission Amount</th>
                        <th>Commission Type</th> -->
                        
						<th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotCollection) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($depotCollection as $RowDepotCollection) 
                    
					<tr>
                        
						<th>{{$serial}}</th>
                        <th>{{$RowDepotCollection->point_name }}</th>
                        <th>{{$RowDepotCollection->retailer_name}}</th>
                        <th>{{$RowDepotCollection->retailer_Address}}</th>
                        <th>{{$RowDepotCollection->collection_amount}}</th>
                        <th>{{$RowDepotCollection->collection_date}}</th>
                        <th>{{$RowDepotCollection->fo_name}}</th>
                        
						<th style="text-align: center;"> 
                                    <a href="{{ URL('/collection/money_recipt/'.$RowDepotCollection->collection_id) }}" target="_blank" title="Click To View Money Recipt">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th>
                        
					<!--	
						<th>{{$RowDepotCollection->commission_amount}}</th>
                        <th>{{$RowDepotCollection->commission_type}}</th> -->
                        
						<th><input type="button" name="collection_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDepotCollection('{{ $RowDepotCollection->collection_id }}')" style="width: 60px;"">
                        <input type="button" name="collection_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDepotCollection('{{ $RowDepotCollection->collection_id}}')" style="width: 60px; margin-top: 0px;"></th>
                    
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
						<th>Depot Name</th>
                        <th>Party Shop Name</th>
                        <th>Address</th>
                        <th>Collection Amount</th>
                        <th>Collection Date</th>
                        <th>Collected By</th>
                        <th>Print</th>
					<!--	
						<th>Commission Amount</th>
                        <th>Commission Type</th> -->
                    
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
