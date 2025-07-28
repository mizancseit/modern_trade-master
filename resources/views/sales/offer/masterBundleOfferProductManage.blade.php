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
            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Offer Name</th>
                        <th>Slab</th>
                        <th>Category</th>
                        <th>Product</th>
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
                        <th>{{ $offers->iMinRange.'-'.$offers->iMaxRange }}</th>
                        <th>{{ $offers->CatName }}</th>
                        <th>{{ $offers->ProName }}</th>
                        <th>
                            <a href="{{ URL('/offers/bundle-offer-pro-edit-new/'.$offers->id) }}">
                            <input type="button" name="route_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" style="width: 70px;">
                            </a>

                            <a href="{{ URL('/offers/bundle-items-delete/'.$offers->id) }}" onclick="return confirm('Are you sure you want to delete this item?');">
                            <input type="button" name="route_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" style="width: 70px; margin-top: 0px;">   
                            </a>
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
                    <th>Slab</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Action</th>
                </tfoot>
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