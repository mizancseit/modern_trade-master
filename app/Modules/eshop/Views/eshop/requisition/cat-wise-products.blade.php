<link href="{{URL::asset('resources/sales/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css')}}" rel="stylesheet">
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-2">
                        <h2>PRODUCTS</h2>
                    </div>
                     
                    <div class="col-sm-3">
                        <h5>Discount for all products</h5>
                    </div>
                    <div class="col-sm-1"  style="text-align: left;">
                        <input type="text" class="form-control" id="discount_all_products" name="commission" value="" style="width: 100px;" autocomplete="off"> 
                    </div>
                    <div class="col-sm-1" style="text-align: right;">
                        <h5>%</h5>
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
                                <th>Order Qty</th>
                                <th>Value</th> 
                                <th>Discount %</th>                                           
                            </tr>
                        </thead>
                        
                        <tbody>
                            @if(sizeof($resultProduct) > 0)
                                @php
                                $serial = 1;
                                @endphp

                                @foreach($resultProduct as $products)

                                <tr>
                                    <td>{{ $serial }}</td>
                                    <input type="hidden" id="price{{$serial}}" name="price[]" value="{{ $products->price }}">
                                    <input type="hidden" id="category_id{{$serial}}" name="category_id[]" value="{{ $products->category_id }}">
                                    <input type="hidden" id="unit{{$serial}}" name="unit[]" value="{{ $products->unit }}">
                                    <input type="hidden" id="produuct_id{{$serial}}" name="produuct_id[]" value="{{ $products->id }}">
                                    <td>{{ $products->name }}</td>
                                   <td>{{ $products->sap_code }}</td>
                                   <input type="hidden" id="sap_code{{$serial}}" name="sap_code[]" value="{{ $products->sap_code }}">
                                    <td><input type="text" class="form-control" id="qty{{$serial}}" name="qty[]"  value="" style="width: 80px;" onkeyup="addQty({{$serial}})" onmouseout="totalsFO();" autocomplete="off">
                                    </td>
                                    <td><input type="number" class="form-control" id="value{{$serial}}" name="value[]" maxlength="3" value="0" style="width: 80px;" readonly=""></td>
                                    <td><input type="number" class="form-control discount_sku_wise" id="discount{{$serial}}" name="item_discount[]" maxlength="3" value="0" style="width: 80px;" ></td>
                                                                 
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

<script type="text/javascript">
    $('#discount_all_products').keyup(function() {
        var discount_all_products = $('#discount_all_products').val();
         $('.discount_sku_wise').val(discount_all_products);
        console.log(discount_all_products);
        // var sumdata=0;
        // $('.discount_sku_wise').each(function(){

        //     if($(this).val()=="")
        //     {
        //         sumdata += parseFloat($(this).val());
        //     }
        // });
        // $("#totalQty").html(sumdata);
    }); 
</script>