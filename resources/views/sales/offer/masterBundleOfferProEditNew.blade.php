@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            OFFER PRODUCT UPDATE
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div>                    
                </div>                
            </div>
        </div>

        @if(Session::has('success'))
            <div class="alert alert-success">
            {{ Session::get('success') }}                        
            </div>
        @endif


        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                    
                <div class="card">
                    <div class="header">
                        <h2>OFFER PRODUCT</h2>                            
                    </div>

                    <form action="{{ URL('/offers/bundle-offer-pro-update-new') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                        <div class="body"> 

                            
                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <div class="form-group">
                                    <div class="form-line">           
                                        <b>Offer <span style="color: #FF0000;">*</span></b>
                                        <p></p>
                                        <div class="form-line">
                                            <select name="offerTypes" id="offerTypes" class="form-control show-tick" data-live-search="true" required="" onchange="bundleOffers(this.value)">                                               
                                                @foreach($resultBundleOffer as $offers)
                                                <option value="{{ $offers->iId }}" @if($resultBundleOfferEdit->offerId==$offers->iId) selected="" @endif>{{ $offers->vOfferName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <input type="hidden" name="offerProId" id="offerProId" value="{{ $resultBundleOfferEdit->id }}">
                                    <input type="hidden" name="proID" id="proID" value="{{ $resultBundleOfferEdit->productId }}">
                                    </div>
                                </div>

                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <div class="form-group">
                                    <div class="form-line">           
	                                    <b>Slab <span style="color: #FF0000;">*</span></b>
	                                    <p></p>
	                                    <div class="form-line">
	                                        <select name="slabs" id="slabs" class="form-control show-tick" data-live-search="true" required="" onchange="bundleOffers(this.value)">	                                            
	                                            @foreach($resultBundleOfferSlab as $offers)
	                                            <option value="{{ $offers->iId }}" @if($resultBundleOfferEdit->slabId==$offers->iId) selected="" @endif>{{ $offers->iMinRange.'-'.$offers->iMaxRange }}</option>
												@endforeach
	                                        </select>
	                                    </div>
	                                </div>
                                    </div>
                                </div>

                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <div class="form-group">
                                    <div class="form-line">           
                                        <b>Type <span style="color: #FF0000;">*</span></b>
                                        <p></p>
                                        <div class="form-line">
                                            <select name="type" id="type" class="form-control show-tick" required="" onchange="showSlabOrProductType(this.value)"> 
                                                <option value="1" @if($resultBundleOfferEdit->productType=='1') selected="" @endif>SSG Products</option> 
                                                <option value="2" @if($resultBundleOfferEdit->productType=='2') selected="" @endif>Gift</option>
                                            </select>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <div class="form-group">
                                    <div class="form-line">           
                                        <b>Category <span style="color: #FF0000;">*</span></b>
                                        <p></p>
                                        <div class="form-line">
                                            <select name="category" id="category" class="form-control show-tick" data-live-search="true" required="" onchange="ssgCategoryWiseProNew(this.value)" > 
                                                @foreach($resultSSGProductCat as $offers)
                                                <option value="{{ $offers->id }}" @if($resultBundleOfferEdit->categoryId==$offers->id) selected="" @endif>{{ $offers->name }}</option>
                                                @endforeach 
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                
                                @if($resultBundleOfferEdit->productType=='1')
                                <div id="ssgPro">
                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="form-group">
                                        <div class="form-line">         
                                            <b>Product <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            <div class="form-line">
                                                <div id="ssgProducts">
                                                    <select name="products" id="products" class="form-control show-tick" data-live-search="true"> 
                                                        @foreach($resultSSGProduct as $offers)
                                                        <option value="{{ $offers->id }}" @if($resultBundleOfferEdit->productId==$offers->id) selected="" @endif>{{ $offers->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="form-group ">
                                            <div class="form-line">
                                                <label for="category">Product Qty <span style="color: #FF0000;">*</span></label>
                                                <div class="form-line">
                                                    <input type="number" name="proQty" class="form-control show-tick" value="{{ $resultBundleOfferEdit->stockQty }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-6 align-left" style="padding-left:0px;">
                                        
                                        <div class="form-line">           
                                            <b>Product Qty <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            
                                                <input type="number" name="proQty" class="form-control show-tick" value="{{ $resultBundleOfferEdit->stockQty }}">
                                            
                                        </div>
                                    </div> --}}
                                </div>
                                @else
                                <div id="ssgPro" style="display: none;">
                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="form-group">
                                        <div class="form-line">         
                                            <b>Product <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            <div class="form-line">
                                                <div id="ssgProducts">
                                                    <select name="products" id="products" class="form-control show-tick" data-live-search="true"> 
                                                        @foreach($resultSSGProduct as $offers)
                                                        <option value="{{ $offers->id }}" @if($resultBundleOfferEdit->productId==$offers->id) selected="" @endif>{{ $offers->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="form-group ">
                                            <div class="form-line">
                                                <label for="category">Product Qty <span style="color: #FF0000;">*</span></label>
                                                <div class="form-line">
                                                    <input type="number" name="proQty" class="form-control show-tick" value="{{ $resultBundleOfferEdit->stockQty }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-6 align-left" style="padding-left:0px;">
                                        
                                        <div class="form-line">           
                                            <b>Product Qty <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            
                                                <input type="number" name="proQty" class="form-control show-tick" value="{{ $resultBundleOfferEdit->stockQty }}">
                                            
                                        </div>
                                    </div> --}}
                                </div>
                                @endif

                                <div id="ssgGift" @if($resultBundleOfferEdit->productType!='2') style="display: none;" @endif>
                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="input-group">           
                                            <b>Gift Name <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            <div class="form-line">
                                                <input type="text" name="giftName" class="form-control show-tick" value="{{ $resultBundleOfferEdit->giftName }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 align-left" style="padding-left:0px;">
                                        <div class="input-group">           
                                            <b>Gift Qty <span style="color: #FF0000;">*</span></b>
                                            <p></p>
                                            <div class="form-line">
                                                <input type="number" name="giftQty" class="form-control show-tick" value="{{ $resultBundleOfferEdit->stockQty }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="submit" value="Submit" class="btn bg-red btn-block btn-sm waves-effect" style="width: 70px;">
                                
                            
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </section>
@endsection