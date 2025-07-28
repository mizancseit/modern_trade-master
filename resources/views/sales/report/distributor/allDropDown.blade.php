<!-- Bootstrap Select Css -->
<link href="{{URL::asset('resources/sales/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

@if($serial==1)
	<select id="products" class="form-control show-tick" data-live-search="true">
        <option value="">------ Product ------</option>
        @foreach($resultProduct as $products)
        	<option value="{{ $products->pid }}">{{ $products->pname }}</option>
        @endforeach  
    </select>

@elseif($serial==2)
	<select id="reason" name="reason" class="form-control show-tick" required="">
		<option value="0">-- No Reason --</option>  
		@foreach($resultReason as $reason)
			<option value="{{ $reason->id }}">{{ $reason->reason }}</option>
		@endforeach  
	</select>
@elseif($serial==3)
	<div class="col-sm-2" id="pointsDiv">
		<select id="points" class="form-control show-tick" data-live-search="true" onchange="allFos()">
			<option value="0">Choose Point</option>  
			@foreach($resultPoints as $points)
				<option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
			@endforeach  
		</select>
	</div>
@elseif($serial==4)
	<select id="reason" name="reason" class="form-control show-tick" required="">
		<option value="0">-- No Reason --</option>  
		@foreach($resultReason as $reason)
			<option value="{{ $reason->id }}">{{ $reason->reason }}</option>
		@endforeach  
	</select>
@elseif($serial==5)
<div class="input-group" id="showHiddenDiv">
    <span class="input-group-addon">
        <i class="material-icons">business</i>
    </span>

    <select id="retailer" name="retailer" class="form-control show-tick" data-live-search="true" required="">
        <option value="">-- Select Retailer --</option>
        @foreach($resultRetailer as $retailer)
            <option value="{{ $retailer->retailer_id }}"> {{ $retailer->name }} </option>
        @endforeach
    </select>
</div>
@endif
