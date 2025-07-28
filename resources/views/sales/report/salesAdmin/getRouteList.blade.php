<select class="form-control show-tick" name="route_id" id="route_id" onchange="getRetailerList(this.value)">
	<option value="">Please Select Route</option>
		@foreach($RouteList as $rowRoute)
			<option value="{{ $rowRoute->route_id }}">{{ $rowRoute->rname }}</option>
		@endforeach
</select>



