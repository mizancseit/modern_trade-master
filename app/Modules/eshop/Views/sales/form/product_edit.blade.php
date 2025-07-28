<form action="{{ URL('/eshop-product-edit-process') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                 <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Product Edit info</h4>
            </div>
            <div class="modal-body">

               <div class="row clearfix">
                    <div class="col-sm-12 col-md-12"> 
                        <div class="col-sm-6 col-md-4"> 
                            <label for="division">Channel:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                   <select id="channel" name="channel" class="form-control" data-live-search="true" required="">
                                        <option value="">-- Select Channel --</option> 
                                          @foreach($resultChannel as $row)
                                        <option value="{{ $row->business_type_id }}" @if ($resultProduct->gid == $row->business_type_id) {{ "selected" }} @endif >{{ $row->business_type }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-6 col-md-4">
                            <label for="division">Company Code:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Company Code" name="company_code"  value="{{ $resultProduct->companyid }}"  autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label for="division">SAP Code:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="SAP Code" name="sap_code"  value="{{ $resultProduct->sap_code }}"  autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                   <div class="col-sm-12 col-md-12">
                        
                        <div class="col-sm-6 col-md-6"> 
                            <label for="division">Category:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                   <select id="category" name="category" class="form-control" data-live-search="true" required="">
                                        <option value="">-- Select Category --</option> 
                                         @foreach($resultCat as $resultCat)
                                        <option value="{{ $resultCat->id }}" @if ($resultProduct->catid == $resultCat->id) {{ "selected" }} @endif>{{ $resultCat->name }}</option>
                                        @endforeach  
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Product Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Product Name" name="product_name" value="{{ $resultProduct->name }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        


                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-4">
                            <label for="division">Depot Price:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Depot Price" name="depot_price"  value="{{ $resultProduct->depo }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label for="division">Distributor Price:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Distributor Price" name="distributor_price"  value="{{ $resultProduct->distri }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4"> 
                            <label for="division">MRP Price:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="MRP Price" name="mrp_price"  value="{{ $resultProduct->mrp }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
    <div class="modal-footer">
       <input type="hidden" id="id" name="id" value="{{ $resultProduct->id }}">
         <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
         <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
    </div>
</form>