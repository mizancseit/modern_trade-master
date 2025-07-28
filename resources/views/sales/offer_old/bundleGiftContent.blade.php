<div class="modal-dialog">
    <div class="modal-content">
    
        <div class="modal-header" style="background-color: #A62B7F">
            {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
            <h4 class="modal-title" id="myModalLabel" > {{ 'Bundle Offer > '.$resultOffer->vOfferName }} </h4>
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
                        ->get();
            @endphp

            @if(sizeof($resultOfferGift)>0)
            <p style="color: #000;margin-left: 40px;"> Please select any one Gift Item </p>


            @foreach($resultOfferGift as $gifts)            
               
                    @if($gifts->productType==2)
                    
                        @php $pdId = $gifts->id; @endphp
                        <p style="margin-left: 40px;">
                        <input name="giftsProduct[]" type="radio" id="radio_789{{ $gifts->id }}" class="radio-col-red" value="{{ $pdId.'_'.$gifts->productType }}">
                        <label for="radio_789{{ $gifts->id }}" style="padding-right: 15px;"> {{ $gifts->giftName }} </label>
                        </p>

                    @endif

                    @if($gifts->productType==1)

                        @php 
                        $pdId = $gifts->giftName;
                        $proName = DB::table('tbl_product')
                        ->select('id','name')
                        ->where('id', $gifts->giftName) 
                        ->first();

                        if(sizeof($proName)>0)
                        {
                            $name = $proName->name;
                        }
                        else
                        {
                            $name = '';
                        }
                        @endphp
                        <p style="margin-left: 40px;">
                        <input name="giftsProduct[]" type="radio" id="radio_789{{ $gifts->id }}" class="radio-col-red" value="{{ $pdId.'_'.$gifts->productType }}">
                        <label for="radio_789{{ $gifts->id }}" style="padding-right: 15px;"> {{ $name }} </label>
                        </p>
                    @endif                
            
            @endforeach
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