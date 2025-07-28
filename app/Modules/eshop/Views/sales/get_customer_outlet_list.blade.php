<select  class="form-control show-tick" name="outlet_id" id="outlet_id" >
	<option value="">Select Outlet</option>
	@foreach($officerlist as $row)
		<option value="{{ $row->party_id }}">{{ $row->name }}</option>
	@endforeach 
</select>


