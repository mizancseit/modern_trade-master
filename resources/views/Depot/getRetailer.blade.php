<select class="form-control show-tick" name="retailer_id" data-live-search="true" required="">
	<option value="">Please Select Retailer</option>
		@foreach($RetList as $rowRetailer)
			<option value="{{ $rowRetailer->retailer_id }}">{{ $rowRetailer->name . '&nbsp;(&nbsp;' . $rowRetailer->retailer_id . '&nbsp;)&nbsp;'  }}</option>
		@endforeach
</select>



