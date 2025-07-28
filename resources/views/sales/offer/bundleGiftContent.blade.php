<div class="modal-dialog">
    <div class="modal-content">
    
        <div class="modal-header" style="background-color: #A62B7F">
            {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
            <h4 class="modal-title" id="myModalLabel" > {{ 'Bundle Offer' }} </h4>
        </div>

        <div class="alert alert-danger alert-dismissible" id="dalert" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            Please check at least one of the product.                       
        </div>
    
        <div class="modal-body">
            
            @php

            $resultOfferGift = DB::table('tbl_bundle_product_details')
                        ->where('offerId', $offerId) 
                        ->where('slabId', $offerRangeId) 
                        ->whereNotNull('groupid') 
                        ->groupBy('groupid') 
                        ->get();
            @endphp

            @if(sizeof($resultOfferGift)>0)
            <p style="color: #000;"> Please select any one Gift Item </p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width="10%">#</td>
                        <td width="75%">Gift Name</td>
                        <td width="15%">Gift Qty</td>
                        <td width="15%">Gift Value</td>
                    </tr>
                </thead>
            
            @foreach($resultOfferGift as $gifts)            
               
                    @if($gifts->productType==2)
                    
                        @php $pdId = $gifts->id; @endphp

                        <tr>
                            <td style="padding: 5px;">
                                
                                <input name="giftsProduct[]" type="radio" id="radio_789{{ $gifts->id }}" class="radio-col-red" value="{{ $pdId.'_'.$gifts->productType.'_'.$gifts->stockQty.'_'.$gifts->categoryId.'_'.$gifts->slabId }}">
                                <label for="radio_789{{ $gifts->id }}" style="padding-right: 15px;"> {{ $gifts->giftName }} </label>
                               
                            </td>
                            <td style="padding: 5px;">{{ $gifts->stockQty }}</td>
                        </tr>
                        

                    @endif

                    @if($gifts->productType==1) 
                    @php $pdId = $gifts->giftName; 
                    $alreadyAdded = DB::table('tbl_order_gift')
                                        ->where('offerid', $offerId)
                                        ->where('groupid', $gifts->groupid)
                                        ->first();
                    @endphp                      
                        <tr>
                            <td style="padding: 5px;">
                                
                                <input name="giftsProduct[]" type="radio" id="radio_789{{ $gifts->id }}" class="radio-col-red" value="{{ $gifts->groupid.'_'.$gifts->productType.'_'.$gifts->slabId }}">

                                <!-- @if(sizeof($alreadyAdded)>0) @if($alreadyAdded->groupid==$gifts->groupid) checked="" @endif @endif -->

                                <label for="radio_789{{ $gifts->id }}" style="padding-right: 15px;"> </label>
                           
						 

                        @php
                            $resultOfferGiftPro = DB::table('tbl_bundle_product_details')
                                        ->where('offerId', $offerId) 
                                        ->where('slabId', $offerRangeId) 
                                        ->where('groupid', $gifts->groupid)
                                        ->get();

                            $s = 1;
                            foreach($resultOfferGiftPro as $items)
                            {                                
                                $proName = DB::table('tbl_product')
                                ->select('id','name','depo')
                                ->where('id', $items->giftName) 
                                ->first();

                                if(sizeof($proName)>0)
                                {
                                    $name = $proName->name;
                                }
                                else
                                {
                                    $name = '';
                                }
                                
                           
                       
                           
						
						if($s>1)
						{   @endphp 
					
						  <tr>	
						   <td style="padding: 5px;"></td>
							<td style="padding: 5px;"> @if($s>1) <b> AND </b> {{ $name }} @else {{ $name }} @endif  </td>
                            <td style="padding: 5px;"> {{ $items->stockQty }} </td>
                            <td style="padding: 5px;"> {{ number_format($items->stockQty * $proName->depo,2)}} </td>
                        </tr>
						
					 @php	} else {   @endphp 
							
							 <td style="padding: 5px;"> @if($s>1) <b> AND </b> {{ $name }} @else {{ $name }} @endif  </td>
                            <td style="padding: 5px;"> {{ $items->stockQty }} </td>
                            <td style="padding: 5px;"> {{ number_format($items->stockQty * $proName->depo,2) }} </td>
                        </tr>
						
					 @php	}
						
                       
                            $s++;
                            }
                        @endphp

                    @endif                
            
            @endforeach

            </table>

            @else
            Sorry , gift not available now.
            @endif
            <p class="debug-url"></p>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            @if(sizeof($resultOfferGift)>0)
            <button type="button" class="btn btn-danger btn-ok" onclick="giftsAdded('{{$offerId}}','{{$status}}')">Add</button>
            @endif
            
        </div>
    </div>
</div>