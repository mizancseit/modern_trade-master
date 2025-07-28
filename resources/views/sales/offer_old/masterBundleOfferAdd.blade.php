@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            OFFER NEW
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
                            <form action="{{ URL('/offers/bundle-submit') }}" id="form" method="POST" onsubmit="return validate();">
			                            {{ csrf_field() }}    <!-- token -->

			                <input type="hidden" name="offerProId" id="offerProId" value="">

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="home">
                                    

			                        <div class="body"> 

			                            
			                                <div class="col-md-6 align-left" style="padding-left:0px;">

			                                	<div class="input-group">
				                                    <b>Offer Name <span style="color: #FF0000;">*</span></b>
				                                    <div class="form-line">
				                                        <input type="text" id="offerName" name="offerName" class="form-control" value="" placeholder="Enter Offer Name" required="" maxlength="50">
				                                    </div>
				                                </div>

				                                <div class="input-group">
				                                    <p> &nbsp;</p>
				                                    <b>From Date <span style="color: #FF0000;">*</span></b>
				                                    <div class="form-line">
				                                        <input type="text" id="fromdate" name="fromdate" class="form-control" value="" placeholder="Enter From Date" required="" readonly="">
				                                    </div>
				                                </div>

				                                <div class="input-group">
				                                    <p> &nbsp;</p>
				                                    <b>To Date <span style="color: #FF0000;">*</span></b>
				                                    <div class="form-line">
				                                        <input type="text" id="todate" name="todate" class="form-control" value="" placeholder="Enter To Date" required="" readonly="">
				                                    </div>
				                                </div>
			                                    
			                                    <p> &nbsp;</p>
			                                    <b>Offer Type <span style="color: #FF0000;">*</span></b>
			                                    <div class="form-line">
			                                        <select name="offerTypes" id="offerTypes" class="form-control show-tick" data-live-search="true" required="" onchange="offerTypesActive()">
			                                            <option value="">--Select Offer Type--</option>
			                                            <option value="1">Regular Offer</option>
														<option value="2">Special Offer</option>
														<option value="3">Bundle Offer</option>
			                                        </select>
			                                    </div>

			                                    <p> &nbsp;</p>
			                                    <b>Business Type <span style="color: #FF0000;">*</span></b>
			                                    <div class="form-line">
			                                        <select name="offerBusinessTypes" id="offerBusinessTypes" class="form-control show-tick" data-live-search="true" required="" onchange="offerCategoryActive()">
			                                            <option value="">--Select Offer Type--</option>
			                                            @foreach($resultBusinessType as $types)
			                                            	<option value="{{ $types->business_type_id }}"> {{ $types->business_type }}</option>
			                                            @endforeach
			                                        </select>
			                                    </div>                                   
			                                                                   

			                                
			                                    <p> &nbsp;</p>
			                                    <b>Status <span style="color: #FF0000;">*</span></b>
			                                    <div class="form-line">
			                                        <select name="offerStatus" id="offerStatus" class="form-control show-tick" data-live-search="true" required="">
			                                            <option value="1">Active</option>
														<option value="2">Inactive</option>
			                                        </select>
			                                    </div>
				                                

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
				                                            	<input type="number" name="from_slab[]" id="from_slab" class="form-control" style=" background-color: #FFF;" disabled="">
				                                            </th>
				                                            <th style="text-align: center; background: #EEEEEE">
				                                            	<input type="number" name="to_slab[]" id="to_slab" class="form-control" style=" background-color: #FFF;" disabled="">
				                                            </th>
				                                            <th style="text-align: center; background: #EEEEEE">
				                                            	<input type="button" name="add" id="add" value="ADD+" class="btn bg-red btn-block btn-lg waves-effect" onClick="addMoreSlab()" disabled="">
				                                            </th>
				                                        </tr>

				                                        <input type="hidden" name="prof_count" id="prof_count" value="1">
										                <tr id="prof_1">
										                  
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
				                                    {{-- <thead style="font-size: 12px;">
				                                        <tr>
				                                            <th>
				                                            	<div class="demo-checkbox">
							                                		<input type="checkbox" id="basic_checkbox_2" onClick="selectAll(this)" class="filled-in" name="chk[]"/>
							                                		<label for="basic_checkbox_2" style="margin-bottom: 0px"></label>
							                                	</div>
						                                	</th>
				                                            <th>ALL</th>                  
				                                        </tr>
				                                    </thead> --}}
				                                    <tbody  style="font-size: 11px; font-weight: normal;">
				                                    	@foreach($resultDivision as $division)
				                                    	<tr>
				                                            <th>
				                                            	<div class="demo-checkbox">
							                                		<input type="checkbox" id="basic_checkbox_2{{ $division->div_id }}" name="divisions[]" value="{{ $division->div_id }}" class="filled-in" onclick="divisionWisePoints()" />
							                                		<label for="basic_checkbox_2{{ $division->div_id }}" style="margin-bottom: 0px"></label>
							                                	</div>
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
				                                	<table class="table table-bordered" style="margin-bottom: 0px;">
				                                    	<tr style="font-size: 11px;">
											                <th colspan="2">No Result</th>
											            </tr>
					                                </table>	                                	
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
				                                	<table class="table table-bordered" style="margin-bottom: 0px;">
				                                    	<tr style="font-size: 11px;">
											                <th colspan="2">No Result</th>
											            </tr>
					                                </table>
				                                </div>

			                                </div>

			                        </div>

			                        <div class="row" style="text-align: center; padding-bottom: 20px;">
			                        <div class="col-sm-12">
			                                <div class="col-sm-2">                                        
			                                    <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Submit</button>
			                                </div>
			                                <div class="col-sm-10">                                        
			                                    <div class="alert alert-danger alert-dismissible" id="dalert" style="display: none;">
									                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
									            	<span id="msgOffer"></span>                      
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