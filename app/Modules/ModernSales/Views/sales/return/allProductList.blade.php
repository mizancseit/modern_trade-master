<link href="{{URL::asset('resources/sales/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css')}}" rel="stylesheet">
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-6">
                        <h2>PRODUCTS</h2>
                    </div>
                   <div class="col-sm-2">
                        <h5>Previous Discount</h5>
                    </div>
                    <div class="col-sm-2"  style="text-align: left;">
                        <input type="text" class="form-control" name="discount" id="discount" value="@if(sizeof($lastDiscount)>0) {{ $lastDiscount->commission }} @else {{ '0.00' }} @endif" readonly="" style="width: 100px;"> 
                    </div>
                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
                    </div>

                </div>                           
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                        
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Name</th>
                                <th>Code</th>
                                <th>Return Qty</th>
                                <th>Return Value</th>                                           
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
                                    <th>{{ $products->sap_code }}</th>
                                    <th><input type="number" class="form-control" id="qty{{$serial}}" name="qty[]" maxlength="3" pattern="[1-9]" min="1" value="" style="width: 80px;" onkeyup="addQty({{$serial}})" onmouseout="totalsFO();">
                                    </th>
                                    <th><input type="number" class="form-control" id="value{{$serial}}" name="value[]" maxlength="3" value="0" style="width: 80px;" readonly=""></th>
                                                                 
                                </tr>
                                @php
                                $serial ++;
                                @endphp

                                @endforeach
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right; padding-top: 17px;" align="right">Total : </th>
                                
                                <th><input type="text" class="form-control" id="totalQty" name="totalQty" readonly="" value="" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalValue" name="totalValue" readonly="" value="" style="width: 80px;"></th>
                               
                            </tr>
                        </tfoot>
                    </table>
            
                </div>


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