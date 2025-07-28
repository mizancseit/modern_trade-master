@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            OFFER PRODUCT NEW
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

                    <form action="{{ URL('/offers/bundle-offer-pro-submit') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                        <div class="body"> 

                            <div class="input-group">
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="input-group">	                                    
	                                    <b>Offer <span style="color: #FF0000;">*</span></b>
	                                    <p></p>
	                                    <div class="form-line">
	                                        <select name="offerTypes" id="offerTypes" class="form-control show-tick" data-live-search="true" required="" onchange="bundleOffers(this.value)">
	                                            <option value="">--Please Select Offer--</option>
	                                            @foreach($resultBundleOffer as $offers)
	                                            <option value="{{ $offers->iId }}">{{ $offers->vOfferName }}</option>
												@endforeach
	                                        </select>
	                                    </div>
	                                </div>

	                                <div class="input-group">
		                                <div class="col-md-12 align-left">
		                                	<div id="offerDetils"></div>
		                                </div>
		                            </div>

                                </div>
                            </div>

                            
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </section>
@endsection