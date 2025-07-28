@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            MASTER OFFER PRODUCT MANAGE
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div> 

                    <div class="col-lg-3">
                        <a href="{{ URL('/offers/bundle-product-add') }}">
                            <button type="button" class="btn bg-success btn-block btn-lg waves-effect">NEW OFFER PRODUCT</button>
                        </a>                        
                    </div>
                </div>                
            </div>
        </div>

        @if(Session::has('success'))
            <div class="alert alert-success">
            {{ Session::get('success') }}                        
            </div>
        @endif

    <div class="card">
	    <div class="header">
	        <h2>
	            OFFER PRODUCT
	        </h2>
	    </div>

    <div class="body">

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                    
                <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Offer Name</th>
                        <th>To Date</th>
                        <th>From Date</th>
                        <th>Offer Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                 <tbody>
                 
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultBundleOffer as $offers) 
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $offers->vOfferName }}</th>
                        <th>{{ $offers->dBeginDate }}</th>
                        <th>{{ $offers->dEndDate }}</th>
                        <th>
                            @if($offers->iOfferType==1)
                            Regular Offer
                            @elseif($offers->iOfferType==2)
                            Special Offer
                            @elseif($offers->iOfferType==3)
                            Bundle Offer
                            @endif
                        </th>
                        <th>
                        	@if($offers->iStatus==0)
                        	   <img src="{{URL::asset('resources/sales/images/icon/if_notok.png')}}">
                        	@else
                        	   <img src="{{URL::asset('resources/sales/images/icon/if_ok.png')}}">
                        	@endif
                        </th>

                        <th>
                            <a href="{{ URL('/offers/bundle-offer-pro-edit/'.$offers->id) }}">
                        	<input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" style="width: 70px;"">
                            </a>
                        	<input type="button" name="route_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" data-target="#BundleOfferActiveOrInactive" style="width: 70px; margin-top: 0px;">
                            {{-- <input type="button" name="route_delete" id="delete" value="View" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" data-target="#BundleOfferDetails" style="width: 70px; margin-top: 0px;"> --}}
                        	<input type="hidden" name="offerid" id="offerid" value="{{ $offers->id }}">
                        </th>
                    </tr>
                   
                
                 	@php
                    $serial++;
                    @endphp
                    @endforeach
                    
               
                </tbody>
                <tfoot>
                    <th>SL</th>
                    <th>Offer Name</th>
                    <th>To Date</th>
                    <th>From Date</th>
                    <th>Offer Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>                   
            </div>
        </div>
    </div>
</div>


    </section>

    <div class="modal fade" id="BundleOfferActiveOrInactive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header" style="background-color: #A62B7F">
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                    <h4 class="modal-title" id="myModalLabel" >Offer Product Delete ! </h4>
                </div>
            
                <div class="modal-body" style="text-align: center;">
                    <p><h4>Are you sure?</h4></p>
                    <p>You want to delete these !</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-ok" onclick="bundleProductActiveOrInactive()">Yes</button>
                    
                </div>
            </div>
        </div>
    </div>
@endsection