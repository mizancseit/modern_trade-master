@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            MASTER OFFER UPDATE
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

        <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        
                        <div class="body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                <li role="presentation" class="active"><a href="#home" data-toggle="tab">OFFER DETAILS</a></li>
                                <li role="presentation"><a href="#profile" data-toggle="tab">OFFER CATEGORY</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <form action="{{ URL('/offers/bundle-update') }}" id="form" method="POST" onsubmit="return validate();">
			                            {{ csrf_field() }}    <!-- token -->

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="home">
			                        <div class="body">
	                                <div class="col-md-6 align-left" style="padding-left:0px;">

	                                	<div class="input-group">
		                                    <b>Offer Name <span style="color: #FF0000;">*</span></b>
		                                    <div class="form-line">
		                                        <input type="text" id="offerName" name="offerName" class="form-control" value="{{ $resultBundleOffer->vOfferName }}" placeholder="Enter Offer Name" required="" maxlength="50">
		                                    </div>
		                                </div>

		                                <div class="input-group">
		                                    <p> &nbsp;</p>
		                                    <b>From Date <span style="color: #FF0000;">*</span></b>
		                                    <div class="form-line">
		                                        <input type="text" id="fromdate" name="fromdate" class="form-control" value="{{ date('d-m-Y', strtotime($resultBundleOffer->dBeginDate)) }}" placeholder="Enter From Date" required="" readonly="">
		                                    </div>
		                                </div>

		                                <div class="input-group">
		                                    <p> &nbsp;</p>
		                                    <b>To Date <span style="color: #FF0000;">*</span></b>
		                                    <div class="form-line">
		                                        <input type="text" id="todate" name="todate" class="form-control" value="{{ date('d-m-Y', strtotime($resultBundleOffer->dEndDate)) }}" placeholder="Enter To Date" required="" readonly="">
		                                    </div>
		                                </div>
	                                    
	                                    <p> &nbsp;</p>
	                                    <b>Offer Type <span style="color: #FF0000;">*</span></b>
	                                    <div class="form-line">
	                                        <select name="offerTypes" id="offerTypes" class="form-control show-tick" data-live-search="true" required="" onchange="offerTypesActive()">
	                                            {{-- <option value="">--Select Offer Type--</option> --}}
	                                            <option value="1" @if($resultBundleOffer->iOfferType==1) selected="selected" @endif>Regular Offer</option>
												<option value="2" @if($resultBundleOffer->iOfferType==2) selected="selected" @endif>Special Offer</option>
												<option value="3" @if($resultBundleOffer->iOfferType==3) selected="selected" @endif>Bundle Offer</option>
												<option value="4" @if($resultBundleOffer->iOfferType==4) selected="selected" @endif>Memo Commission</option>
	                                        </select>
	                                    </div>

	                                    <p> &nbsp;</p>
	                                    <b>Business Type <span style="color: #FF0000;">*</span></b>
	                                    <div class="form-line">
	                                        <select name="businessType" id="businessType" class="form-control show-tick" data-live-search="true" required="">
	                                        	@foreach($resultBusinessType as $types)
	                                            	<option value="{{ $types->business_type_id }}" @if($types->business_type_id==$resultBundleOffer->iBusinessType) selected="selected" @endif> {{ $types->business_type }}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>                              

	                               
	                                    <p> &nbsp;</p>
	                                    <b>Status <span style="color: #FF0000;">*</span></b>
	                                    <div class="form-line">
	                                        <select name="offerStatus" id="offerStatus" class="form-control show-tick" data-live-search="true" required="">
	                                            <option value="1" @if($resultBundleOffer->iStatus==1) selected="selected" @endif>Active</option>
												<option value="2" @if($resultBundleOffer->iStatus==2) selected="selected" @endif>Inactive</option>
	                                        </select>
	                                    </div>
		                                

		                                <input type="hidden" name="offerid" value="{{ $resultBundleOffer->iId }}">

	                                    <p> &nbsp;</p>                                    
	                                    <table class="table table-bordered">
	                                    	<thead>
		                                        <tr>
		                                            <th colspan="3" style="text-align: center; border-bottom: 1px solid #FFF; background: #EEEEEE">Slab</th>
		                                        </tr>
		                                    </thead>
	                                    	<thead>
		                                        <tr>
		                                            <th style="text-align: center; background: #EEEEEE">From</th>
		                                            <th style="text-align: center; background: #EEEEEE">To</th>
		                                            <th style="text-align: center; background: #EEEEEE"></th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                        <tr>
		                                            <th style="text-align: center; background: #EEEEEE">
		                                            	<input type="number" name="from_slab[]" id="from_slab" class="form-control" style=" background-color: #FFF;" @if($resultBundleOffer->iOfferType!=3) disabled="" @endif>
		                                            </th>
		                                            <th style="text-align: center; background: #EEEEEE">
		                                            	<input type="number" name="to_slab[]" id="to_slab" class="form-control" style=" background-color: #FFF;" @if($resultBundleOffer->iOfferType!=3) disabled="" @endif>
		                                            </th>
		                                            <th style="text-align: center; background: #EEEEEE">
		                                            	<input type="button" name="add" id="add" value="ADD+" class="btn bg-red btn-block btn-lg waves-effect" onClick="addMoreSlab()" @if($resultBundleOffer->iOfferType!=3) disabled="" @endif>
		                                            </th>
		                                        </tr>

		                                        @php
							                	$slabSerial = 1;
							                	@endphp

							                	@foreach($resultBundleSlab as $slabs)
									                <tr style="background:#BFBFBF" id="prof_{{ $slabSerial }}">
			                                            <th style="text-align: center;">
			                                            	<input type="number" id="from_slab1{{ $slabSerial }}" name="from_slab1[]" value="{{ $slabs->iMinRange }}" class="form-control" style="padding:5px 10px;background-color: #FFF; font-weight: normal;" readonly="">
			                                            </th>
			                                            <th style="text-align: center;">
			                                            	<input type="number" id="to_slab1{{ $slabSerial }}" name="to_slab1[]" value="{{ $slabs->iMaxRange }}" class="form-control" style="padding:5px 10px;background-color: #FFF; font-weight: normal;" readonly="">
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
	                                </div>

	                                
	                                <div class="col-md-2 align-left" style="padding-left:0px;">

	                                    <table class="table table-bordered">
	                                    	<thead>
		                                        <tr>
		                                            <th colspan="2" style="text-align: center; background: #EEEEEE">Division</th>                                       
		                                        </tr>
		                                    </thead>
		                                    
		                                    <tbody  style="font-size: 11px; font-weight: normal;">
		                                    	@foreach($resultDivision as $division)

		                                    	@php
		                                    	$resultDivScope = DB::table('tbl_bundle_offer_scope')
		                                    					->where('iOfferId', $resultBundleOffer->iId)
		                                    					->where('iDivId', $division->div_id)
		                                    					->first();

		                                    	$divid = '';
		                                    	if(sizeof($resultDivScope) >0 )
		                                    	{
		                                    		$divid = $resultDivScope->iDivId;
		                                    	}
		                                    	@endphp

		                                    	<tr>
		                                            <th>
		                                            	<!-- <div class="demo-checkbox"> -->
					                                		<input type="checkbox" id="basic_checkbox_2{{ $division->div_id }}" name="divisions[]" value="{{ $division->div_id }}" class="filled-in" onclick="divisionWisePoints()" @if($divid!='') checked="" @endif />
					                                		<label for="basic_checkbox_2{{ $division->div_id }}" style="margin-bottom: 0px"></label>
					                                	<!-- </div> -->
					                                </th>
		                                            <th>{{ $division->div_name }}</th>                  
		                                        </tr>
		                                        @endforeach
		                                    </tbody>
		                                </table>
	                                </div>

	                                <div class="col-md-2 align-left" style="padding-left:0px;">
	                                    
	                                    <table class="table table-bordered" style="margin-bottom: 0px;">
	                                    	<thead>
		                                        <tr>
		                                            <th colspan="2" style="text-align: center; background: #EEEEEE">Point</th>                                       
		                                        </tr>
		                                    </thead>
		                                </table>

		                                <div id="pointsDiv">
		                                	@php
	                                    	$resultDivArray = DB::table('tbl_bundle_offer_scope')->select('iDivId')
	                                    					->where('iOfferId', $resultBundleOffer->iId)
	                                    					->whereNotNull('iDivId')
	                                    					->whereNull('iPointId')
	                                    					->whereNull('iRouteId')
	                                    					->get();
	                                    	
	                                    	$resultDivArray = collect($resultDivArray)->map(function($x){ return (array) $x; })->toArray();

	                                    	$resultPointArray = DB::table('tbl_bundle_offer_scope')
	                                    					->select('iDivId','iPointId','iRouteId')
	                                    					->whereNotNull('iPointId')
	                                    					->where('iOfferId', $resultBundleOffer->iId)
	                                    					->whereIn('iDivId', $resultDivArray)
	                                    					->get();
	                                    	//$resultPointArray = $resultPointArray->toArray();

	                                    	$resultPointArray = collect($resultPointArray)->map(function($x){ return (array) $x; })->toArray();

	                                    	//print_r($resultPointArray);

	                                    	$resultPoints = DB::table('tbl_point')
					                        ->select('tbl_point.point_id','tbl_point.point_name','tbl_point.point_division','tbl_division.div_id','tbl_division.div_name','tbl_bundle_offer_scope.iDivId','tbl_bundle_offer_scope.iPointId','tbl_bundle_offer_scope.iRouteId','tbl_bundle_offer_scope.iOfferId')

					                         ->leftJoin('tbl_division', 'tbl_point.point_division', '=', 'tbl_division.div_id')
					                         ->leftJoin('tbl_bundle_offer_scope', 'tbl_point.point_id', '=', 'tbl_bundle_offer_scope.iPointId')
					                         ->whereIn('tbl_point.point_division', $resultDivArray)
					                         ->where('tbl_point.business_type_id', $resultBundleOffer->iBusinessType)
					                         ->groupBy('tbl_point.point_id')
					                         ->orderBy('tbl_division.div_id', 'ASC')
					                         ->get();
	                                    					
	                                    	@endphp
	                                    	
	                                    	@if(sizeof($resultPoints)>0)
	                                    	<div id="pointsScroll">
												<table class="table table-bordered">										    	        
											        <tbody style="font-size: 11px;">
											        	@foreach($resultPoints as $points)
											        	@php
											        		$mPoints = DB::table('tbl_bundle_offer_scope')
											        				->where('iOfferId', $resultBundleOffer->iId)
											        				->where('iDivId', $points->point_division)
											        				->where('iPointId', $points->point_id)
											        				->first();

				                                    		$mP="";
				                                    		if(sizeof($mPoints)>0)
				                                    		{
				                                    			$mP=$mPoints->iPointId;
				                                    		}			                                    		
											        	@endphp
											        	
											        	<tr>
											                <th>
											                	<!-- <div class="demo-checkbox"> -->
											                		<input type="checkbox" id="basic_checkbox_4{{ $points->point_id }}" name="points[]" value="{{ $points->point_id }}" class="filled-in" onclick="divisionAndPointWiseroutes()" @if($mP!="") checked="" @endif/>

											                		<label for="basic_checkbox_4{{ $points->point_id }}" style="margin-bottom: 0px"></label>
											                		<input type="hidden" name="pointsID[]" id="pointsID" value="{{ $points->point_division.'_'.$points->point_id }}">
											                	<!-- </div> -->
											                </th>
											                <th>{{ $points->div_name }} - {{ $points->point_name }}</th>                  
											            </tr>
											            @endforeach
											        </tbody>
											    </table>
											</div>
									        @else
									        	<table class="table table-bordered" style="margin-bottom: 0px;">
			                                    	<tr style="font-size: 11px;">
										                <th colspan="2">No Result</th>
										            </tr>
				                                </table>
									        @endif                             	
		                            </div>
	                            </div>

	                            <div class="col-md-2 align-left" style="padding-left:0px;">
	                                    
	                                <table class="table table-bordered" style="margin-bottom: 0px;">
	                                	<thead>
	                                        <tr>
	                                            <th colspan="2" style="text-align: center; background: #EEEEEE">Route</th>                                       
	                                        </tr>
	                                    </thead>
	                                </table>

	                                <div id="routesDiv">
	                                	@php	                                	
	                                	$resultRoutes = DB::table('tbl_route')
						                    ->select('tbl_route.route_id','tbl_route.rname','tbl_route.point_id','tbl_point.point_id','tbl_point.point_name','tbl_point.point_division','tbl_point.business_type_id')                        
						                    ->join('tbl_point', 'tbl_route.point_id', '=', 'tbl_point.point_id')
						                    ->join('tbl_division', 'tbl_point.point_division', '=', 'tbl_division.div_id')
						                    ->where('tbl_point.business_type_id', $resultBundleOffer->iBusinessType)
						                    ->whereIn('tbl_point.point_id', $resultPointArray)
						                    ->orderBy('tbl_route.point_id', 'ASC')
						                    ->get();

	                                	@endphp

	                                	@if(sizeof($resultRoutes)>0)
	                                	<div id="pointsScroll">
											<table class="table table-bordered">    	        
										        <tbody style="font-size: 11px;">
										        	
											        	@foreach($resultRoutes as $routes)

											        	@php
											        		$mRoutes = DB::table('tbl_bundle_offer_scope')
											        				->select('iOfferId','iDivId','iPointId','iRouteId')
											        				->where('iOfferId', $resultBundleOffer->iId)
											        				->where('iDivId', $routes->point_division)
											        				->where('iPointId', $routes->point_id)
											        				->where('iRouteId', $routes->route_id)
											        				->first();

				                                    		$mR="";
				                                    		if(sizeof($mRoutes)>0)
				                                    		{
				                                    			$mR=$mRoutes->iRouteId;
				                                    		}			                                    		
											        	@endphp
											        	<tr>
											                <th>
											                	{{-- <div class="demo-checkbox"> --}}
											                		<input type="checkbox" id="md_checkbox_40{{ $routes->route_id }}" name="routes[]" value="{{ $routes->route_id }}" class="filled-in" @if($mR!="") checked="" @endif/>
											                		<label for="md_checkbox_40{{ $routes->route_id }}" style="margin-bottom: 0px"></label>
											                		<input type="hidden" name="routesID[]" id="routesID" value="{{ $routes->point_division.'_'.$routes->point_id.'_'.$routes->route_id }}">
											                	{{-- </div> --}}
											                </th>
											                <th>{{ $routes->point_name }} - {{ $routes->rname }}</th>                  
											            </tr>
											            @endforeach											        
										        </tbody>
										    </table>
										</div>
										@else
								        	<table class="table table-bordered" style="margin-bottom: 0px;">
		                                    	<tr style="font-size: 11px;">
									                <th colspan="2">No Result</th>
									            </tr>
			                                </table>
								        @endif
	                                </div>
	                            </div>

	                            <div class="row" style="text-align: center;">
		                            <div class="col-sm-12">
		                                <div class="col-sm-2">                                        
		                                    <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Submit</button>
		                                </div>
		                                <div class="col-sm-10">                                        
		                                    <div class="alert alert-danger alert-dismissible" id="dalert" style="display: none;">
								                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
								            	Please check at least one of the division.                     
								            </div>
		                                </div>
		                            </div>
	                            </div>
	                        </div>
			                    
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="profile">
                                	<p></p>
                                	* Note : Please select first business type 
                                	<p></p>
                                	<div id="offerCategory">
	                                    @if(sizeof($resultCategory)>0)
										<div id="pointsScroll">
										<table class="table table-bordered">
										    <thead>
										    <tr>
										    <th colspan="2" style="text-align: center; background: #EEEEEE">Category</th>
										    </tr>
										    </thead>

										    <tbody  style="font-size: 11px; font-weight: normal;">
										    @foreach($resultCategory as $category)
										    @php
										    $selected = DB::table('tbl_bundle_category')
										    			->where('offerId',$resultBundleOffer->iId)
										    			->where('categoryId',$category->id)
										    			->first();

										   	$yes = '';
										    if(sizeof($selected)>0)
										    {
										    	$yes = $selected->categoryId;
										    }

										    @endphp
										    <tr>
										    <th>
										    <div class="demo-checkbox">
										    <input type="checkbox" id="basic_checkbox_222{{ $category->id }}" name="category[]" value="{{ $category->id }}" class="filled-in" @if($category->id==$yes
										    	) checked="" @endif />
										    <label for="basic_checkbox_222{{ $category->id }}" style="margin-bottom: 0px"></label>
										    </div>
										    </th>
										    <th>{{ $category->g_code.'::'.$category->name }}</th>                  
										    </tr>
										    @endforeach
										    </tbody>
										    </table>
										</div>
										@else
										<table class="table table-bordered">
										    <thead>
										    <tr>
										    <th colspan="2" style="text-align: center; background: #EEEEEE">Category</th>
										    </tr>
										    </thead>

										    <tbody  style="font-size: 11px; font-weight: normal;">
										    <tr>
										    <th> No Result </th>
										    </tr>
										    </tbody>
										</table>
										@endif
		                            </div>
                                </div>
                                
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    </section>
@endsection