<select  class="form-control show-tick" name="pointID" id="pointID" onchange="tsmPointWiseFos(this.value)">
	<option value="">Select Point</option>
	@foreach($resultPoints as $points)
		<option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
	@endforeach 
</select>


