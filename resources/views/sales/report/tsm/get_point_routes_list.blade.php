<select  class="form-control show-tick" name="routeID" id="routeID">
	<option value="">Select Route</option>
	@foreach($route_list as $routes)
	<option id="ok" value="{{$routes->route_id}}">{{$routes->rname}}</option>
	@endforeach
</select>


