<select  class="form-control show-tick" name="pointsID" id="pointsID" onchange="getFoPerformanceList(this.value)" data-live-search="true">
<option value="">Select Point</option>
@foreach($resultPoints as $points)
<option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
@endforeach 
</select>


