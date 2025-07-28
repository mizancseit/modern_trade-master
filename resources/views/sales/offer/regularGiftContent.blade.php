<form action="{{ URL('show-regular-products-submit') }}" method="post"> 
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">  
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #A62B7F">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                <h4 class="modal-title" id="myModalLabel" > Regular Offer </h4>
            </div>

            <div class="alert alert-danger alert-dismissible" id="dalert" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                Please check at least one of the product.                       
            </div>

            <div class="modal-body">
                 <table class="table" style="margin-bottom: 0px;">
                        <tr style="padding: 0px;">
                            <td style="padding: 0px;" width="80%">
                                <b> Product Name </b>
                            </td>
                            <td style="padding: 0px;" width="10%">
                                <b> Slab </b>
                            </td>
                            <td style="padding: 0px;" width="10%">
                                <b> Qty </b>
                            </td>
                        </tr>
                    </table>
               
                @if(sizeof($regularOfferProducts)>0)
                @php
                $ms=0;
                @endphp 
                @foreach($regularOfferProducts as $categories)
                @php


                echo '<b style="color: #000;">'.$categories->name.'</b><br />';
                
                $specialPro = DB::table('tbl_order_free_qty')
                ->select('tbl_order_free_qty.catid','tbl_order_free_qty.free_id','tbl_order_free_qty.sku_id','tbl_order_free_qty.slab','tbl_order_free_qty.total_free_qty','tbl_order_free_qty.product_id','tbl_order_free_qty.status','tbl_order_free_qty.total_free_qty','tbl_product.name')
                ->join('tbl_product','tbl_order_free_qty.product_id','=','tbl_product.id')
                ->where('tbl_order_free_qty.order_id', $orderid)
                ->whereNotNull('tbl_order_free_qty.slab')
                ->where('tbl_order_free_qty.catid', $categories->catid)
                ->orderBy('tbl_order_free_qty.slab')                        
                ->orderBy('tbl_order_free_qty.total_free_qty')                        
                ->get(); 

                @endphp 

                <p style="margin-left: 50px;">
                    <table class="table" style="margin-bottom: 0px;">
                        
                        @foreach($specialPro as $gifts)
                            @php
                            $specialProAnd = DB::table('tbl_order_regular_and_free_qty AS asi')

                                    ->select('asi.catid','asi.slab','asi.total_free_qty','asi.product_id','asi.status','asi.total_free_qty','asi.special_id','tbl_product.name')
                                    ->join('tbl_product','asi.product_id','=','tbl_product.id')

                                    ->where('asi.special_id', $gifts->free_id)                                    
                                    ->where('asi.order_id', $orderid)
                                    ->first();
                            @endphp
                            <tr style="padding: 0px;">
                                <td style="padding: 0px;" width="80%">
                                    
                                    <input name="giftsProduct[{{ $categories->catid }}{{ $gifts->slab }}{{ $gifts->sku_id }}]" type="radio" id="radio_30{{ $ms }}" class="radio-col-red" value="{{ $gifts->product_id }}_{{ $gifts->slab }}" @if($gifts->status=="0") checked="" @endif>

                                    <label for="radio_30{{ $ms }}" style="padding-right: 15px;"> {{ $gifts->name }} </label> <br />
                                </td>
                                <td style="padding: 0px;" width="10%">
                                    {{ $gifts->slab }}
                                </td>
                                <td style="padding: 0px;" width="10%">
                                    {{ $gifts->total_free_qty }}
                                </td>

                                @if(sizeof($specialProAnd)>0)
                                <tr style="padding: 0px;">
                                    <td style="padding: 0px; padding-left: 25px;" width="80%">
                                        <span style="background:#EEEEEE; padding-left: 5px;padding-right: 5px;margin-right: 5px; "> And </span> {{ $specialProAnd->name }} 
                                    </td>
                                    <td style="padding: 0px;" width="10%">
                                        {{ $specialProAnd->slab }}
                                    </td>
                                    <td style="padding: 0px;" width="10%">
                                        {{ $specialProAnd->total_free_qty }}
                                    </td>
                                </tr>                                
                                @endif
                            </tr>
                            <tr>
                                <td colspan="3" height="10"></td>
                            </tr>
                        @php                
                        $ms++;               
                        @endphp 
                        @endforeach
                    </table>        
                </p>

                @endforeach
                @else
                Sorry , gift not available now.
                @endif
                <p class="debug-url"></p>
            </div>

            <input type="hidden" name="orderid" value="{{ $orderid }}">

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>            
                <button type="submit" class="btn btn-danger btn-ok">Add</button>
            </div>
        </div>
    </div>
</form>