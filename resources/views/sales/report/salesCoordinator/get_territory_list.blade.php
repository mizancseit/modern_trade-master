<select  class="form-control show-tick" name="territory" id="territory" onchange="foPerformancePoints()" data-live-search="true">
<option value="">Select Territory</option>
@foreach($resultTerritory as $territory)
<option value="{{ $territory->id }}">{{ $territory->name }}</option>
@endforeach 
</select>


