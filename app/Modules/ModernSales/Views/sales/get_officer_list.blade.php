<select  class="form-control show-tick" name="fos" id="fos" onchange="allCustomer(this.value)">
	<option value="">Select Officer</option>
	@foreach($officerlist as $row)
		<option value="{{ $row->id }}">{{ $row->display_name }}</option>
	@endforeach 
</select>


