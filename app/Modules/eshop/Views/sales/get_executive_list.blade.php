<select  class="form-control show-tick" name="executive_id" id="executive_id" onchange="allOfficer(this.value)">
	<option value="">Select Executive</option>
	@foreach($executivelist as $row)
		<option value="{{ $row->id }}">{{ $row->display_name }}</option>
	@endforeach 
</select>


