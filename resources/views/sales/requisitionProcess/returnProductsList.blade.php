<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-10">
                        <h2>PRODUCTS LIST</h2>
                    </div>

                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
                    </div>

                </div>                           
            </div>
            <div class="body">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Return Product</th>
                                <th>Qty</th>
                                <th>Value</th>
					        
							</tr>
                        </thead>

                        <tbody>
                            @if(sizeof($resultProduct) > 0)
                                @php
                                $serial = 1;
                                @endphp

                                @foreach($resultProduct as $products)
                                <tr>
                                    <th>{{ $serial }}</th>
                                    <input type="hidden" id="price{{$serial}}" name="price[]" value="{{ $products->price }}">
                                    <input type="hidden" id="category_id{{$serial}}" name="category_id[]" value="{{ $products->category_id }}">
                                    <input type="hidden" id="unit{{$serial}}" name="unit[]" value="{{ $products->unit }}">
                                    <input type="hidden" id="produuct_id{{$serial}}" name="produuct_id[]" value="{{ $products->id }}">
                                    <th>{{ $products->name }}</th>
                                    
									<th><input type="text" class="form-control" id="returnQty{{$serial}}" name="return_qty[]" maxlength="3" style="width: 80px;" onkeyup="addReturn({{$serial}})" onmouseout="totalsFOM();"></th>                                            
                                    
									<th><input type="text" class="form-control" id="returnValue{{$serial}}" name="return_value[]" maxlength="3" style="width: 80px;"></th>                                            
                        		
								</tr>
                                @php
                                $serial ++;
                                @endphp

                                @endforeach
                            @endif

                            <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right; padding-top: 17px;" align="right">Total : </th>
                                
                                <th><input type="text" class="form-control" id="totalQty" name="totalQty" readonly="" value="0" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalValue" name="totalValue" readonly="" value="0" style="width: 80px;"></th>                                
                               
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                    <p></p>
                    <div class="row">
                        <div class="col-sm-10" style="text-align: right;">
                            &nbsp;
                        </div>

                        <div class="col-sm-2" style="text-align: right;">
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
                        </div>
                    </div>                    
            </div>
        </div>
    </div>
</div>