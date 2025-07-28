<!-- Default Size -->
	
<form action="{{ URL('/collectionEditProcess') }}" method="post">
           
               
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Collection Info</h4>
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
											
											<?php if($depotCollection[0]->point_id == $rowDepot->point_id) { ?>
												<option value="{{ $rowDepot->point_id }}" selected>{{ $rowDepot->point_name }}</option>
											<?php } else { ?>
												<option value="{{ $rowDepot->point_id }}">{{ $rowDepot->point_name }}</option>
											<?php }  ?>
											
											@endforeach
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Retailer:*</label>
                                    <div class="form-group">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="retailer_id" required="" onchange="getRetailerInvoiceEdit(this.value)">
											<option value="">Please Select Retailer</option>
											@foreach($retailerList as $rowRetailer)
											
											<?php if($depotCollection[0]->retailer_id == $rowRetailer->retailer_id) { ?>
												
												<option value="{{ $rowRetailer->retailer_id }}" selected>{{ $rowRetailer->name }}</option>
											
											<?php } else { ?>
											
												<option value="{{ $rowRetailer->retailer_id }}">{{ $rowRetailer->name }}</option>
											
											<?php }  ?>
											
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
											
											<?php if($depotCollection[0]->collect_by == $rowFo->id) { ?>
												<option value="{{ $rowFo->id }}" selected>{{ $rowFo->display_name }}</option>
											<?php } else { ?>
												<option value="{{ $rowFo->id }}">{{ $rowFo->display_name }}</option>
											<?php }  ?>
											
											 @endforeach
                                        </select>

                                        </div>
                                    </div>
									
									
									<label for="division">Invoice No:</label> 
                                    <div class="form-group">
										<div class="form-line" id="invoice_no_edit">
                                            <select class="form-control show-tick" name="invoice_no">
                                                <option value="">Select Invoice</option>
                                                <option value="{{$depotCollection[0]->invoice_no}}" selected>{{$depotCollection[0]->invoice_no}}</option>

                                            </select>
                                        </div>
                                    </div>
									
								
									
									<label for="division">Money Recipt:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Money Recipt" name="reference_no" value="{{$depotCollection[0]->reference_no}}" required="" />
                                        </div>
                                    </div>
									
                                	
									<label for="division">Collection Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Collection Amount" name="collection_amount" 
											value="{{$depotCollection[0]->collection_amount}}" required="" />
                                        </div>
                                    </div>
									
									<label for="division">Collection Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="fromdate" placeholder="Collection Date" name="collection_date"
											value="{{$depotCollection[0]->collection_date}}" required="" />
                                        </div>
                                    </div>
									
									<!-- 
									<label for="division">Commission Type:*</label>
									<div class="form-group">
                                        <div class="form-line">
                                      
									  <select class="form-control show-tick" name="commission_type" >
                                        <option value="">Please Select Commission Type</option>
                                        
											<option value="REGULAR" <?php //echo ($depotCollection[0]->commission_type=='REGULAR')?'selected':''?>>REGULAR</option>
											<option value="SPECIAL" <?php //echo ($depotCollection[0]->commission_type=='SPECIAL')?'selected':''?>>SPECIAL</option>
							            
                                        </select>

                                        </div>
                                    </div>
									
									<label for="division">Commission Amount:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Commission Amount" 
											value="{{$depotCollection[0]->commission_amount}}" name="commission_amount" />
                                        </div>
                                    </div>

								-->			
									
									
                                </div>
                            </div>
                        </div>
                        
						<input type="hidden" name="id" value="{{$depotCollection[0]->collection_id}}">
                        
						<div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                        </div>
                        
                    </form>
	