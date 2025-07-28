<select class="form-control show-tick" name="retailer_id" required="">
	<option value="">Please Select Retailer</option>
		@foreach($RetList as $rowRetailer)
			<option value="{{ $rowRetailer->retailer_id }}">{{ $rowRetailer->name }}</option>
		@endforeach
</select>



