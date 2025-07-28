@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            
			<div class="block-header">
                <div class="row">
                    
					<div class="col-lg-12">
                        <h2 style="padding-bottom: 10px;">
                            NEW ORDER
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit-exception') }}"> Visit </a> / New Order
                            </small>
                        </h2>
                    </div>
                   
				    @php
                    $s=1;
                    @endphp
					@foreach($orderBucketSum as $allBuckets)
						<div class="col-lg-3">                        
							<div class="info-box-2 bg-red">
								<div class="icon">
									<i class="material-icons">shopping_cart 1</i>
								</div>
								<a href="{{ URL('/bucket-exception/'.$pointID.'/'.$routeid.'/'.$retailderid . '/'.$allBuckets->partial_order_id.'/'.Request::segment(4)) }}" style="text-decoration: none;" title="Click To Bucket Details">
									<div class="content">
										<div class="text">BUCKET {{ $s }}</div>
										<div class="number count-to" data-from="0" data-to="{{ $allBuckets->grand_total_value }}" data-speed="1000" data-fresh-interval="20">{{ $allBuckets->grand_total_value }}</div>
									</div>
								</a>
							</div>
						</div>
                    @php
                    $s++;
                    @endphp
                    @endforeach
					
					
					
					
					
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif  

			@if(Session::has('failed'))
                <div class="alert alert-danger">
                {{ Session::get('failed') }}                        
                </div>
            @endif  			
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultRetailer)>0) {{ $resultRetailer->name }} @else STORE @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allOrderProductsExc()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->name }}</option>
                                    @endforeach                           
                                </select>
                                {{-- <select class="form-control show-tick">
                                    <option value="">-- Please select subcategory--</option>             
                                </select> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/add-to-cart-exception') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="distributor_id" name="distributor_id" value="{{ $distributorID }}">
                <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                <input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $retailderid }}">
                <input type="hidden" id="offerTypeExc" name="offerTypeExc" value="{{ Request::segment(4) }}">

                <div id="showHiddenDiv">                        
                    {{-- Here Product List --}}
                </div>
            </form> 

        </div>
    </section>
@endsection