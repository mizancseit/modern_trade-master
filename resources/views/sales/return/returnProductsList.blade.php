<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-10">
                        <h2>PRODUCTS LIST</h2>
                    </div>

                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect" {{-- onclick="checkReturnValueAndChangeValue()" --}}>ADD CART</button>
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
                                <th>Category</th>
                                <th>Product</th>
                                <th>Qty</th>        
                                <th>Value</th>      
                            
                            </tr>
                        </thead>
                        
                        <tbody>
                            @if(sizeof($resultProduct) > 0)
                                @php
                                $serial = 1;
                                $returnValue = 0;
                                $changeValue = 0;
                                @endphp

                                @foreach($resultProduct as $products)

                                @php

                                    $returnOrderQty  = DB::table('tbl_return_details')
                                            ->select('return_qty','return_value','change_product_id','change_qty','change_value')
                                            ->where('return_order_id',$return_id)
                                            ->where('change_product_id',$products->id)
                                            ->first();

                                @endphp
                                <tr>
                                    <th>{{ $serial }}</th>
                                    <input type="hidden" id="price{{$serial}}" name="price[]" value="{{ $products->price }}">
                                    <input type="hidden" id="category_id{{$serial}}" name="category_id[]" value="{{ $products->category_id }}">
                                    <input type="hidden" id="unit{{$serial}}" name="unit[]" value="{{ $products->unit }}">
                                    <input type="hidden" id="produuct_id{{$serial}}" name="produuct_id[]" value="{{ $products->id }}">
                                    <th>{{ $products->name }}</th>
                                     @if(sizeof($returnOrderQty) > 0)
                                    <th><input type="text" class="form-control" id="returnQty{{$serial}}" name="return_qty[]" value="{{$returnOrderQty->return_qty}}" maxlength="8" style="width: 80px;" onkeyup="addReturn({{$serial}})" readonly=""></th>                                            
                                    
                                    <th><input type="text" class="form-control" id="returnValue{{$serial}}" name="return_value[]"  value="{{$returnOrderQty->return_value}}" maxlength="8" style="width: 80px;" readonly=""></th> 
                                    @else  
                                    <th><input type="text" class="form-control" id="returnQty{{$serial}}" name="return_qty[]" maxlength="3" style="width: 80px;" onkeyup="addReturn({{$serial}})"></th>                                            
                                    
                                    <th><input type="text" class="form-control" id="returnValue{{$serial}}" name="return_value[]" maxlength="3" style="width: 80px;"></th> 
                                    @endif                                      
                                
                                <th>
                                @php
                                $tbl_return_exception = DB::table('tbl_return_exception')
                                ->where('cat_id',$categoryID)->first();

                                $array = array($categoryID);
                                if($categoryID==4)
                                {
                                    $array = array('4','5');
                                }
                                else if($categoryID==11)
                                {
                                    $array = array('11','5');
                                }
                                else if($categoryID==5)
                                {
                                    $array = array('5','11');
                                }
                                else if($categoryID==35 || $categoryID==27 || $categoryID==29 || $categoryID==30 || $categoryID==31)
                                {
                                    $array = array('27','29','30','31','35');
                                }
                                else if($categoryID==32)
                                {
                                    $array = array('27','29','30','31','32','35','54','67','68');
                                }

                                //print_r($array);

                                $categoryEx=DB::table('tbl_product_category')
                                        ->where('status',0)
                                        ->where('gid', Auth::user()->business_type_id)
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->whereIn('id', $array)
                                        ->get();

                                //dd($categoryEx);

                                @endphp

                                <div class="form-line" style="width: 170px;">
                                    @if(sizeof($tbl_return_exception)>0)
                                    <select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
                                        
                                        <?php
                                        foreach($categoryEx as $pname) 
                                        {

                                        ?>                                  
                                        <option value="{{ $pname->id }}" @if($pname->id==$categoryID) selected="" @endif> {{ $pname->name }} </option>

                                        <?php
                                        }
                                        ?>                                      
                                    </select>
                                    @else
                                    <select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
                                        
                                        <?php
                                        foreach($pcategory as $pname) 
                                        {
                                            if($pname->id==$categoryID)
                                            {
                                        ?>                                  
                                        <option value="{{ $pname->id }}" selected=""> {{ $pname->name }} </option>

                                        <?php
                                            }
                                        }
                                        ?>                                      
                                    </select>
                                    @endif

                                                                       
                                </div>  

                                </th>   
                                @php
                                if(sizeof($returnOrderQty) > 0)
                                $proID = $returnOrderQty->change_product_id;
                                else
                                    $proID ='';
                                @endphp
                                <th>
                                    <div class="form-line" id="div_change_product{{$serial}}">
                                        <select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
                                            <option value="">Select Product</option>
                                            @foreach($resultProductDropDown as $cname)
                                            <option value="{{ $cname->id }}" @if ($proID == $cname->id) {{ "selected" }} @else {{""}} @endif>{{ $cname->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="0">
                                    </div>  
                                </th>           
                                @if(sizeof($returnOrderQty) > 0)    
                                <th>
                                    <input type="text" class="form-control" id="changeQty{{$serial}}" name="change_qty[]" value="{{$returnOrderQty->change_qty}}" maxlength="3" style="width: 80px;" onkeyup="addChange({{$serial}})">
                                </th>                                            
                                    
                                <th>
                                    <input type="text" class="form-control" id="changeValue{{$serial}}" name="change_value[]" value="{{$returnOrderQty->change_value}}" maxlength="3" style="width: 80px;">
                                </th>                                            
                                @else
                                <th>
                                    <input type="text" class="form-control" id="changeQty{{$serial}}" name="change_qty[]" maxlength="3" style="width: 80px;" onkeyup="addChange({{$serial}})">
                                </th>                                            
                                    
                                <th>
                                    <input type="text" class="form-control" id="changeValue{{$serial}}" name="change_value[]" maxlength="3" style="width: 80px;">
                                </th> 
                                @endif
                                
                                </tr>
                                @php
                                $serial ++;
                                @endphp

                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <input type="hidden" style="color: #000;" id="totalReturnValue" value="{{ $returnValue }}">
                    <input type="hidden" style="color: #000;" id="totalChangeValue" value="{{ $changeValue }}">
                    <p></p>
                    <div class="row">
                        <div class="col-sm-10" style="text-align: right;">
                            &nbsp;
                        </div>

                        <div class="col-sm-2" style="text-align: right;">
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect" {{-- onclick="checkReturnValueAndChangeValue()" --}}>ADD CART</button>
                        </div>
                    </div>                    
            </div>
        </div>
    </div>
</div>