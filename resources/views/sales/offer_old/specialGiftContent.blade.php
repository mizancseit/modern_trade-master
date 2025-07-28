<form action="{{ URL('show-special-products-submit') }}" method="post"> 
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">  
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #A62B7F">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                <h4 class="modal-title" id="myModalLabel" > Special Offer </h4>
            </div>

            <div class="alert alert-danger alert-dismissible" id="dalert" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                Please check at least one of the product.                       
            </div>

            <div class="modal-body">

                @if(sizeof($specialOfferProducts)>0)
                @php
                $ms=0;
                @endphp 
                @foreach($specialOfferProducts as $categories)
                @php


                echo '<b style="color: #000;">'.$categories->name.'</b><br />';
                $specialPro = DB::table('tbl_special_order_free_qty')
                ->select('tbl_special_order_free_qty.catid','tbl_special_order_free_qty.slab','tbl_special_order_free_qty.total_free_qty','tbl_special_order_free_qty.product_id','tbl_special_order_free_qty.status','tbl_special_order_free_qty.total_free_qty','tbl_product.name')
                ->join('tbl_product','tbl_special_order_free_qty.product_id','=','tbl_product.id')
                ->where('tbl_special_order_free_qty.order_id', $orderid)
                ->where('tbl_special_order_free_qty.catid', $categories->catid)
                ->orderBy('tbl_product.name')                        
                ->get(); 

                @endphp 

                <p style="margin-left: 50px;">
                    <table class="table" style="margin-bottom: 0px;">
                        <tr style="padding: 0px;">
                            <td style="padding: 0px;">
                                <b> Product Name </b>
                            </td>
                            <td style="padding: 0px;">
                                <b> Slab </b>
                            </td>
                            <td style="padding: 0px;">
                                <b> Qty </b>
                            </td>
                        </tr>
                        @foreach($specialPro as $gifts)
                            <tr style="padding: 0px;">
                                <td style="padding: 0px;">
                                    
                                    <input name="giftsProduct[{{ $categories->catid }}{{ $gifts->slab }}]" type="radio" id="radio_30{{ $ms }}" class="radio-col-red" value="{{ $gifts->product_id }}_{{ $gifts->slab }}" @if($gifts->status=="0") checked="" @endif>

                                    <label for="radio_30{{ $ms }}" style="padding-right: 15px;"> {{ $gifts->name }} </label> <br />
                                </td>
                                <td style="padding: 0px;">
                                    {{ $gifts->slab }}
                                </td>
                                <td style="padding: 0px;">
                                    {{ $gifts->total_free_qty }}
                                </td>
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