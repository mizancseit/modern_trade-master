<select  class="form-control show-tick" name="customer_id" id="customer_id" required="">
	<option value="">Select Customer</option>
	@foreach($officerlist as $row)
		<option value="{{ $row->customer_id }}">{{ $row->name }}</option>
	@endforeach 
</select>


