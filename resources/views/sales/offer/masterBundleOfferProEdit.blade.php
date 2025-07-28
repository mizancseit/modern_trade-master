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

                    <form action="{{ URL('/offers/bundle-offer-pro-update') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                        <div class="body"> 

                            <div class="input-group">
                                <div class="col-md-12 align-left" style="padding-left:0px;">
                                    <div class="input-group">	                                    
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

	                                <div class="input-group">
		                                <div class="col-md-12 align-left">
		                                	<div id="offerDetils">

                                                <table class="table table-bordered">                
                                                    <tbody style="font-size: 11px;">            
                                                        <tr>
                                                            <th colspan="2" style="background-color: #CCC; text-align: center; font-size: 16px; color: #000;">{{ $resultBundleOfferEdit->vOfferName }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="text-align: center;">To Date</th>
                                                            <th style="text-align: center;">From Date</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="text-align: center;"> {{ date('d-m-Y',strtotime($resultBundleOfferEdit->dBeginDate)) }} </th>
                                                            <th style="text-align: center;"> {{ date('d-m-Y',strtotime($resultBundleOfferEdit->dEndDate)) }} </th>
                                                        </tr>

                                                        <!-- Slab List -->
                                                        @if($resultBundleOfferEdit->iOfferType==3)
                                                            @if(sizeof($resultBundleOfferSlab)>0)
                                                                <tr>
                                                                    <th colspan="2" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">SLAB</th>
                                                                </tr>

                                                                @foreach($resultBundleOfferSlab as $slabs)
                                                                <tr>
                                                                    <th style="text-align: center;"> 
                                                                        <div class="demo-radio-button">
                                                                            <input name="slabs" type="radio" id="radio_7{{ $slabs->iId }}" class="radio-col-red" value="{{ $slabs->iId }}" @if(sizeof($resultBundleOfferSlabSelected)>0) @if($resultBundleOfferSlabSelected->slabId==$slabs->iId) checked="" @endif @endif>
                                                                            <label for="radio_7{{ $slabs->iId }}"></label>
                                                                        </div>
                                                                    </th>
                                                                    <th style="text-align: center;"> {{ $slabs->iMinRange.' - '.$slabs->iMaxRange }}</th>
                                                                </tr>
                                                                @endforeach                                                            
                                                            @endif
                                                        @endif

                                                        <tr>
                                                            <th colspan="2">
                                                                <input name="type" type="radio" id="radio_777" class="radio-col-red" value="1" onclick="showSlabOrProduct(1)" @if($resultBundleOfferEdit->productType==1) checked="" @endif>
                                                                <label for="radio_777"> SSG Products </label>  
                                                            </th>               
                                                        </tr>

                                                        <tr>                
                                                            <th colspan="2">
                                                                <input name="type" type="radio" id="radio_788" class="radio-col-red" value="2" onclick="showSlabOrProduct(2)"  @if($resultBundleOfferEdit->productType==2) checked="" @endif>
                                                                <label for="radio_788"> Gift </label>  
                                                            </th>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="2" id="showSlabOrProduct1" style="display: none;"></td>
                                                        </tr>
                                                        
                                                        @if($resultBundleOfferEdit->productType==2)
                                                        <tr>
                                                            <td colspan="2" id="showSlabOrProduct">
                                                                <table class="table table-bordered">                
                                                                    <tbody style="font-size: 11px;">
                                                                        <tr>
                                                                            <th colspan="3" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">GIFT</th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="text-align: center; background: #EEEEEE">
                                                                                <input type="text" name="from_slab[]" id="from_slab" class="form-control" style=" background-color: #FFF;" placeholder="Enter Gift Name">
                                                                            </th>
                                                                            <th style="text-align: center; background: #EEEEEE">
                                                                                <input type="number" name="to_slab[]" id="to_slab" class="form-control" style=" background-color: #FFF;" placeholder="Enter Qty">
                                                                            </th>
                                                                            <th style="text-align: center; background: #EEEEEE">
                                                                                <input type="button" name="add" id="add" value="ADD+" class="btn bg-red btn-block btn-lg waves-effect" onClick="addMoreSlabPro()">
                                                                            </th>
                                                                        </tr>

                                                                        @php
                                                                        $slabSerial = 1;
                                                                        @endphp

                                                                        @foreach($resultBundleOfferSlabOrProduct as $slabs)
                                                                            <tr style="background:#BFBFBF" id="prof_{{ $slabSerial }}">
                                                                                <th style="text-align: center;">
                                                                                    <input type="text" id="from_slab1{{ $slabSerial }}" name="from_slab1[]" value="{{ $slabs->giftName }}" class="form-control" style="padding:5px 10px;background-color: #FFF; font-weight: normal;" readonly="">
                                                                                </th>
                                                                                <th style="text-align: center;">
                                                                                    <input type="number" name="to_slab1[]" id="to_slab1{{ $slabSerial }}" value="{{ $slabs->stockQty }}" class="form-control" style="padding:5px 10px;background-color: #FFF; font-weight: normal;" readonly="">
                                                                                </th>
                                                                                <th style="text-align: center; background: #EEEEEE;background:#F44336">
                                                                                    <img src="{{ URL::asset('resources/sales/images/icon/ic_delete.png')}}" id="prof_{{ $slabSerial }}"  value="prof_{{ $slabSerial }}" onClick="del({{ $slabSerial }})" style="margin-top:5px;cursor:pointer;" alt="Delete slab" title="Delete this slab">
                                                                                </th>
                                                                            </tr>
                                                                        @php
                                                                        $slabSerial++;
                                                                        @endphp

                                                                        @endforeach

                                                                        <input type="hidden" name="prof_count" id="prof_count" value="{{$slabSerial}}">
                                                                        <tr id="prof_{{$slabSerial}}">
                                                                            
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        @endif


                                                       
                                                            @if($resultBundleOfferEdit->productType==1)
                                                            <table class="table table-bordered" id="hiddenPro">                
                                                                <tbody style="font-size: 11px;">
                                                                    <tr>
                                                                        <th colspan="3" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">SSG PRODUCT</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th colspan="3">
                                                                            <div class="input-group">
                                                                                <select name="category" id="category" class="form-control show-tick" data-live-search="true" required="" onchange="ssgCategoryWisePro1(this.value)">
                                                                                    
                                                                                    @foreach($resultSSGProductCat as $pro)
                                                                                    <option value="{{ $pro->id }}" @if(sizeof($resultSSGProductCatSelected)>0) @if($resultSSGProductCatSelected->categoryId==$pro->id) selected="" @endif @endif >{{ $pro->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </th>
                                                                    </tr>

                                                                    <tr colspan="3">
                                                                      <td>
                                                                            <table class="table table-bordered" id="ssgProducts">                
                                                                                <tbody style="font-size: 11px;">
                                                                                    <tr>
                                                                                        
                                                                                        <th colspan="23" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;"> {{ $resultCategory->name }} WISE PRODUCT <br />
                                                                                        TOTAL PRODUCT : {{ sizeof($resultSSGProduct) }}
                                                                                    </th>
                                                                                    </tr>

                                                                                    @if(sizeof($resultSSGProduct)>0)
                                                                                    @foreach($resultSSGProduct as $pros)
                                                                                    <tr>
                                                                                        <th colspan="2"> {{ $pros->name }}</th>
                                                                                        <th>
                                                                                            <div class="form-line">
                                                                                                 <input type="hidden" id="pName{{ $pros->id }}" name="pName[]" class="form-control" value="{{ $pros->id }}">
                                                                                                <input type="number" id="pqty{{ $pros->id }}" name="pqty[]" class="form-control" value="@if($pros->slabId==$resultSSGProductCatSelected->slabId){{ $pros->stockQty}}@endif" placeholder="Enter Qty" maxlength="8">
                                                                                            </div>
                                                                                        </th>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                    @else
                                                                                    <tr>
                                                                                        <th colspan="3"> No Product Found</th>
                                                                                    </tr>
                                                                                    @endif
                                                                                </tbody>
                                                                            </table>
                                                                      </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            @endif
                                                        

                                                        <div id="ssgProducts1">

                                                        </div>

                                                        <tr>                
                                                            <th colspan="2" style="text-align: left;">
                                                                <div class="col-lg-4"></div> 

                                                                <div class="col-lg-3">
                                                                    <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Submit</button>
                                                                </div>             
                                                            </th>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                            </div>
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