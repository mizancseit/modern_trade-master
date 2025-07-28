<select class="form-control show-tick" name="manager_id" id="manager_id" onchange="allExecutive(this.value)">
	<option value="">Select Manager</option>
	@foreach($managerlist as $row)
		<option value="{{ $row->id }}">{{ $row->display_name }}</option>
	@endforeach 
</select>


