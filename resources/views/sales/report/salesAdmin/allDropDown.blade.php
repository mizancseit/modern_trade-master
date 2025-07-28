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
@endif
